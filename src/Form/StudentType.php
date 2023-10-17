<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule')
            ->add('lastname',null,[
                'label' => 'Nom'
            ])
            ->add('firstname',null,[
                'label' => 'Prénom(s)'
            ])
            ->add('adresse')
            ->add('parcours')
            ->add('years',null,[
                'label' => 'Année universitaire'
            ])
            ->add('niveau')
            ->add('photos', FileType::class, [
                'label' => 'Télécharger une photo',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
