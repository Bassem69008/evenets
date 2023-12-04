<?php

namespace App\Form;

use App\Entity\Subject;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control my-2'],
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control my-2'],
                'label' => 'Description',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Conférence' => 'Conférence',
                    'Atelier' => 'Atelier',
                ],
                'attr' => ['class' => 'form-control my-2'],
            ])
            ->add('duration', ChoiceType::class, [
                'choices' => [
                    'Court: 15min' => 'Court: 15min',
                    'Moyen: 30min' => 'Moyen: 30min',
                    'Long: 45min' => 'Long: 45min',
                ],
                'attr' => ['class' => 'form-control my-2'],
            ])
            ->add('speacker_id', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'proposer un présentateur',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getLastname().' '.$user->getFirstname();
                },
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }
}
