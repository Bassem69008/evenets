<?php

namespace App\Form;

use App\Entity\Events;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
class CreateEventType extends AbstractType
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
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subjects', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-control js-select2'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}