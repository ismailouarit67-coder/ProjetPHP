<?php

namespace App\DataFixtures;

use App\Entity\Contribution;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $project = new Project();
            $project->setTitle($faker->sentence(4));
            $project->setDescription($faker->paragraphs(3, true));

            $goal = $faker->numberBetween(2000, 20000);
            $project->setGoalAmount((float) $goal);

            // date fin entre 7 et 60 jours
            $project->setEndDate($faker->dateTimeBetween('+7 days', '+60 days'));

            // contributions 0..25
            $nb = $faker->numberBetween(0, 25);
            $current = 0.0;

            for ($j = 0; $j < $nb; $j++) {
                $c = new Contribution();
                $amount = (float) $faker->numberBetween(20, 800);
                $c->setAmount($amount);
                $c->setDonorName($faker->name());
                $c->setMessage($faker->optional(0.5)->sentence(10));
                $c->setProject($project);

                $current += $amount;

                $manager->persist($c);
            }

            $project->setCurrentAmount($current);
            $manager->persist($project);
        }

        $manager->flush();
    }
}
