<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username', TextType::class,
                [
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your username should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotBlank([
                            'message' => 'Please enter a username',
                        ]),
                    ],
                    'attr' => [
                        'style' => 'width: 100%;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;'
                    ],
                ]
            )
            ->add(
                'Department', TextType::class,
                [
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your Department should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotBlank([
                            'message' => 'Please enter a Department',
                        ]),
                    ],
                ]
            )
            ->add(
                'position',
                TextType::class,
                [
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your position should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotBlank([
                            'message' => 'Please enter a position',
                        ]),
                    ],
                    'attr' => [
                        'style' => 'width: 100%;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;'
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your email should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotBlank([
                            'message' => 'Please enter a email',
                        ]),
                    ],
                    'attr' => [
                        'style' => 'width: 100%;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;'
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
            'validation_groups' => ['Default'],
            'constraints' => [
                new Valid()
            ],
        ]);
    }
}