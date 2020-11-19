<?php

namespace App\Form;

use App\Entity\Salon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifierSalonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom : ', 'attr' => array('placeholder' => "Saisir nom du salon", 'class' => 'form-control', )))
            ->add('date', DateType::class, array('label' => 'Date : '))
            ->add('ville', TextType::class, array('label' => 'Ville : ', 'attr' => array('placeholder' => "Saisir ville du salon", 'class' => 'form-control', )))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salon::class,
        ]);
    }
}
