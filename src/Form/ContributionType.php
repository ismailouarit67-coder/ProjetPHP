<?php

namespace App\Form;

use App\Entity\Contribution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class ContributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('donorName', TextType::class, [
                'required' => false,
                'label' => 'Votre nom (optionnel)',
            ])
            ->add('amount', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant du don',
                'constraints' => [
                    new GreaterThan(0),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'label' => 'Message (optionnel)',
                'attr' => ['rows' => 3],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contribution::class,
        ]);
    }
}
