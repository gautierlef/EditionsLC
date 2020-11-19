<?php

namespace App\Form;

use App\Entity\Auteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifierAuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom : ', 'attr' => array('placeholder' => "Saisir nom de l'auteur", 'class' => 'form-control')))
            ->add('prenom', TextType::class, array('label' => 'Prénom : ', 'attr' => array('placeholder' => "Saisir prénom de l'auteur", 'class' => 'form-control')))
            ->add('ville', TextType::class, array('label' => 'Ville : ', 'attr' => array('placeholder' => "Saisir ville de l'auteur", 'class' => 'form-control')))
            ->add('mdp', TextType::class, array('label' => 'Mot de passe : ', 'attr' => array('placeholder' => "Saisir mot de passe de l'auteur", 'class' => 'form-control')))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Auteur::class,
        ]);
    }
}
