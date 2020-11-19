<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ModifierMdpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ancienMdp', PasswordType::class, array('label' => 'Ancien mot de passe : ', 'attr' => array('placeholder' => "Saisir ancien mot de passe", 'class' => 'form-control')))
            ->add('nouveauMdp', PasswordType::class, array('label' => 'Nouveau mot de passe : ', 'attr' => array('placeholder' => "Saisir nouveau mot de passe", 'class' => 'form-control')))
            ->add('confirmerMdp', PasswordType::class, array('label' => 'Confirmer nouveau mot de passe : ', 'attr' => array('placeholder' => "Confirmer nouveau mot de passe", 'class' => 'form-control')))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
