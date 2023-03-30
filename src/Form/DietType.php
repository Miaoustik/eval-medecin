<?php

namespace App\Form;

use App\Entity\Diet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DietType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nouveau nom du régime : ',
                'label_attr' => [
                    'class' => 'text-secondary form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sans sel ...'
                ]
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary text-white w-100 mt-4 shadow1'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diet::class,

        ]);
    }
}
