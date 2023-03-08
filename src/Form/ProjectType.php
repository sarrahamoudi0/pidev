<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Choice ;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Valid;


class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the project Name',
                    ]),
                ],
            ])

            ->add('theme', ChoiceType::class, [
                'choices'=> [
                    '1- NO POVERTY' => 'NO POVERTY',
                    '2- ZERO HUNGER' => 'ZERO HUNGER',
                    '3- GOOD HEALTH AND WELL BEING' => 'GOOD HEALTH AND WELL BEING',
                    '4- QUALITY EDUCATION' => 'QUALITY EDUCATION',
                    '5- GENDER EQUALITY'=>'GENDER EQUALITY',
                    '6- CLEAN WATER AND SANITATION'=>'CLEAN WATER AND SANITATION',
                    '7- AFFORDABLE AND CLEAN ENERGY' => 'AFFORDABLE AND CLEAN ENERGY',
                    '8- DECENT WORK AND ECONOMIC GROWTH'=>'DECENT WORK AND ECONOMIC GROWTH',
                    '9- INDUSTRY, INNOVATION AND INFRASTRUCTURE'=>'INDUSTRY, INNOVATION AND INFRASTRUCTURE',
                    '10- REDUCED INEQUALITIES' => 'REDUCED INEQUALITIES',
                    '11- SUSTAINABLE CITIES AND COMMUNITIES'=>'SUSTAINABLE CITIES AND COMMUNITIES',
                    '12- RESPONSIBLE CONSUMPTION AND PRODUCTION'=>'RESPONSIBLE CONSUMPTION AND PRODUCTION',
                    '13- CLIMATE ACTION'=>'CLIMATE ACTION',
                    '14- LIFE BELOW WATER'=>'LIFE BELOW WATER',
                    '15- LIFE ON LAND'=>'LIFE ON LAND',
                    '16- PEACE, JUSTICE AND STRONG INSTITUTIONS'=>'PEACE, JUSTICE AND STRONG INSTITUTIONS',
                    '17- PARTNERSHIPS FOR THE GOALS'=>'PARTNERSHIPS FOR THE GOALS',
                ],

            ])

            ->add('description',TextareaType::class , [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the project description',
                    ]),
                ],
            ])
            ->add('location',TextType::class , [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the project location',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the project image(.png .gif .jpg .jpeg)',
                    ]),
                ],
                'attr' => [
                    'accept' => "image/x-png,image/x-gif,image/x-jpeg,
                image/jpeg,image/gif,image/png
                "
                ],
                'mapped' => false,
                'required' => false,

            ])
            ->add("submit", SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'validation_groups' => ['Default'],
            'constraints' => [
                new Valid(),
            ],
        ]);
    }
}