<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Contribution;
use App\Form\ContributionType;
use App\Repository\ContributionRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/projects/{id}', name: 'project_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        ContributionRepository $contributionRepository
    ): Response {
        // Formulaire de contribution
        $contribution = new Contribution();
        $contribution->setProject($project);

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        // Soumission form
        if ($form->isSubmitted()) {

            // Bloquer si projet clos
            if ($project->getStatus() === 'closed') {
                $this->addFlash('danger', 'Ce projet est clôturé. Vous ne pouvez plus contribuer.');
                return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
            }

            if ($form->isValid()) {

                // ✅ Stripe token (envoyé par ton JS Stripe)
                $token = $request->request->get('stripeToken');
                if (!$token) {
                    $this->addFlash('danger', 'Veuillez saisir une carte bancaire.');
                    return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
                }

                // ✅ Paiement Stripe (TEST)
                try {
                    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

                    \Stripe\Charge::create([
                        'amount' => (int) round(((float) $contribution->getAmount()) * 100), // centimes
                        'currency' => 'eur',
                        'description' => 'Contribution au projet : ' . $project->getTitle(),
                        'source' => $token,
                    ]);
                } catch (\Throwable $e) {
                    $this->addFlash('danger', 'Paiement refusé : ' . $e->getMessage());
                    return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
                }

                // ✅ Mise à jour montant collecté (seulement si paiement OK)
                $project->setCurrentAmount($project->getCurrentAmount() + $contribution->getAmount());

                // Fermer projet si objectif atteint
                if ($project->getCurrentAmount() >= $project->getGoalAmount()) {
                    $project->setStatus('closed');
                }

                // Sauvegarde contribution
                $em->persist($contribution);
                $em->flush();

                $this->addFlash('success', 'Paiement réussi ! Merci pour votre contribution.');
                return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
            }
        }

        // Dernières contributions
        $lastContributions = $contributionRepository->findBy(
            ['project' => $project],
            ['contributedAt' => 'DESC'],
            10
        );

        // Progression
        $goal = (float) $project->getGoalAmount();
        $current = (float) $project->getCurrentAmount();
        $percent = $goal > 0 ? min(100, round(($current / $goal) * 100, 2)) : 0;

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'lastContributions' => $lastContributions,
            'percent' => $percent,
            'form' => $form->createView(),
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }
}
