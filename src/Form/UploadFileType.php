<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UploadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class,[
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label'=> 'Fichier'])
        ->add('submit',SubmitType::class,[
            'attr'=>['class'=>'btn btn-primary']
        ])
        ;


    }
}
