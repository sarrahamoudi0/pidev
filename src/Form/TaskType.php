<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;


class TaskType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the Task Name',
                    ]),
                ],
            ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $this->security->getUser();

                if ($this->security->isGranted('ROLE_ORGANIZATION')) {
                    $form->add('members', EntityType::class, array(
                        'class' => Member::class,
                        'multiple' => true,
                        'choice_label' => "username",
                        
                    )
                    );
                }
            })




            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the Task Description',
                    ]),
                ],
            ])


            ->add("submit", SubmitType::class)
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'validation_groups' => ['Default'],
            'constraints' => [
                new Valid(),
            ],
        ]);
    }
}