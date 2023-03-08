<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice ;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid ;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('Rtype',ChoiceType::class,[
            'choices'=> [
        'Partnership' => 'Partnership',
        'Participation' => 'Participation',
                'Sponsoship' => 'Sponsoship',
    ],
                'constraints' => [new Choice(['choices' => ['Partnership', 'Participation','Sponsoship']])],
            ])
            ->add("submit",SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
            'validation_groups' => ['Default'],
            'constraints' => [
                new Valid(),
            ],
        ]);

    }
}
