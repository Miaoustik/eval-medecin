<?php

namespace App\Form;

use App\Entity\Allergen;
use App\Entity\Diet;
use App\Entity\User;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientType extends AbstractType
{
    /** @var Allergen[] $allergens */
    private array $allergens = [];

    /** @var Diet[] $diets */
    private array $diets = [];

    private bool $fetched = false;

    public function __construct(
        private readonly AllergenRepository $allergenRepository,
        private readonly DietRepository $dietRepository
    )
    {

    }

    private function getData($type, $text) {

        if ($this->fetched === false) {
            $this->allergens = $this->allergenRepository->findAll();
            $this->diets = $this->dietRepository->findAll();
            $this->fetched = true;
        }

        if ($type === 'allergen')  {
            $newAllergens = [];
            foreach ($this->allergens as $allergen) {
                $method = 'get' . ucfirst($text);
                $newAllergens[] = $allergen->$method();
            }
            return $newAllergens;
        } else {
            $newDiets = [];
            foreach ($this->diets as $diet) {
                $method = 'get' . ucfirst($text);
                $newDiets[] = $diet->$method();
            }
            return $newDiets;
        }

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label_attr' => [
                    'class' => 'text-secondary form-label'
                ],
                'label' => 'Email : '
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label_attr' => [
                    'class' => 'text-secondary form-label'
                ],
                'label' => 'Mot de passe : '
            ])
            /*->add('allergens', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => $this->allergenRepository->findAll(),
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'choice_attr' => ChoiceList::attr($this, function () {
                        return ['class' => 'form-check-input'];
                    })
                ]
            ])*/
            ->add('allergens', ChoiceType::class, [
                'choices' => $this->allergenRepository->findAll(),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_attr' => ChoiceList::attr($this, function () {
                    return ['class' => 'form-check-input'];
                })
            ])
            /*->add('diets', ChoiceType::class, [
                'choices' => $this->dietRepository->findAll(),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_attr' => ChoiceList::attr($this, function () {
                    return ['class' => 'form-check-input'];
                })
            ])*/
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary text-white w-100 mt-4 shadow1',
                ],
                'label' => 'Modifier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
