<?php

namespace App\DataFixtures;

use App\Entity\Allergen;
use App\Entity\Diet;
use App\Entity\Ingredient;
use App\Entity\IngredientRecipe;
use App\Entity\Notice;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadIngredient($manager);
        $this->loadAllergens($manager);
        $this->loadDiet($manager);
        $this->loadUsers($manager);
        $this->loadRecipes($manager);
    }

    private function loadIngredient(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $name = "Ingredient $i";
            $ingredient = (new Ingredient())
                ->setName($name);
            $manager->persist($ingredient);
            $this->addReference($name, $ingredient);
        }
        $manager->flush();
    }

    private function loadAllergens(ObjectManager $manager): void
    {
        $fruits = (new Allergen())
            ->setName('Fruits de mer');
        $manager->persist($fruits);
        $this->addReference('Allergen 0', $fruits);

        $noix = (new Allergen())
            ->setName('Noix');
        $manager->persist($noix);
        $this->addReference('Allergen 1', $noix);

        $lactose = (new Allergen())
            ->setName('Lactose');
        $manager->persist($lactose);
        $this->addReference('Allergen 2', $lactose);

        $gluten = (new Allergen())
            ->setName('Gluten');
        $manager->persist($gluten);
        $this->addReference('Allergen 3', $gluten);

        $manager->flush();
    }

    private function loadDiet(ObjectManager $manager): void
    {
        $sel = (new Diet())
            ->setName('Sans sel');
        $manager->persist($sel);
        $this->addReference('Diet 0', $sel);

        $sucre = (new Diet())
            ->setName('Sans sucre');
        $manager->persist($sucre);
        $this->addReference('Diet 1', $sucre);

        $vegetarian = (new Diet())
            ->setName('Végétarien');
        $manager->persist($vegetarian);
        $this->addReference('Diet 2', $vegetarian);

        $gras = (new Diet())
            ->setName('Pauvre en graisse');
        $manager->persist($gras);
        $this->addReference('Diet 3', $gras);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {
            $user = (new User())
                ->setEmail("user$i@test.fr");
            $user->setPassword($this->hasher->hashPassword($user, 'test'));
            $user->setRoles(['ROLE_USER']);

            if ($i < 1) {
                /** @var Allergen $allergen */
                $allergen = $this->getReference('Allergen 0', Allergen::class);

                /** @var Diet $diet */
                $diet = $this->getReference('Diet 0', Diet::class);

            } elseif ($i < 2) {
                /** @var Allergen $allergen */
                $allergen = $this->getReference('Allergen 1', Allergen::class);

                /** @var Diet $diet */
                $diet = $this->getReference('Diet 1', Diet::class);

            } elseif ($i < 3) {
                /** @var Allergen $allergen */
                $allergen = $this->getReference('Allergen 2', Allergen::class);

                /** @var Diet $diet */
                $diet = $this->getReference('Diet 2', Diet::class);

            } else {
                /** @var Allergen $allergen2 */
                $allergen2 = $this->getReference('Allergen 2', Allergen::class);
                $user->addAllergen($allergen2);

                /** @var Allergen $allergen */
                $allergen = $this->getReference('Allergen 3', Allergen::class);

                /** @var Diet $diet */
                $diet = $this->getReference('Diet 3', Diet::class);

            }
            $user->addAllergen($allergen);
            $user->addDiet($diet);

            $manager->persist($user);
            $this->addReference("User $i", $user);
        }
        $manager->flush();
    }

    public function loadRecipes (ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {


            $numberDiet = rand(0, 4);
            $numberAllergen = rand(0, 4);
            $numberIngredient = rand(0, 5);

            $recipe = new Recipe();
            $recipe->setTitle("Recipe $i")
                ->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                 Duis congue consequat leo, eu vulputate eros placerat sed. Donec pretium mi quis mauris rhoncus 
                 sagittis. Nulla eu mi facilisis, convallis ipsum vitae, porttitor ex.");


            $recipe->setBreakTime(rand(0, 70))
                ->setPreparationTime(rand(0, 70))
                ->setCookingTime(rand(0, 70));

            if ($numberDiet == 4) {
                $diet = null;
            } else {
                /** @var Diet $diet */
                $diet = $this->getReference("Diet $numberDiet", Diet::class);
            }

            if ($numberAllergen == 4) {
                $allergen = null;
            } else {
                /** @var Allergen $allergen */
                $allergen = $this->getReference("Allergen $numberAllergen", Allergen::class);
            }

            if ($allergen) {
                $recipe->addAllergens($allergen);
            }

            if ($diet) {
                $recipe->addDiet($diet);
            }


            if ($i % 5 === 0) {
                $recipe->setPatientOnly(true);
            }

            $recipe->setStages([
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis congue
                 consequat leo, eu vulputate eros placerat sed.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis congue
                 consequat leo, eu vulputate eros placerat sed.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis congue
                 consequat leo, eu vulputate eros placerat sed.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis congue
                 consequat leo, eu vulputate eros placerat sed.'
            ]);

            for ($e = 0; $e < 15; $e++) {

                $notice = new Notice();

                if ($e < 5) {
                    $notice->setNote(5);
                    /** @var User $user */
                    $user = $this->getReference("User 0", User::class);
                } elseif ($e < 10) {
                    $notice->setNote(4);
                    /** @var User $user */
                    $user = $this->getReference("User 1", User::class);
                } else {
                    $notice->setNote(3);
                    /** @var User $user */
                    $user = $this->getReference("User 2", User::class);
                }
                $notice->setRecipe($recipe);
                $notice->setUser($user);
                $notice->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.");

                $manager->persist($notice);
                $recipe->addNotice($notice);
            }

            $mesures = [' g', ' grammes', '', ' cl'];

            for ($g = 0; $g < $numberIngredient; $g++) {
                $numberMesure = rand(0, 3);

                $recipe->addIngredientRecipe((new IngredientRecipe())
                    ->setRecipe($recipe)
                    ->setIngredient($this->getRandomIngredient())
                    ->setQuantity('5' . $mesures[$numberMesure]));
            }

            $manager->persist($recipe);
        }
        $manager->flush();
    }


    private function getRandomIngredient(): Ingredient
    {
        $number = rand(0, 4);

        /** @var Ingredient $ingredient */
        $ingredient = $this->getReference("Ingredient $number", Ingredient::class);
        return $ingredient;
    }
}
