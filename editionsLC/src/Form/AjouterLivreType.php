<?php

namespace App\Form;

use App\Entity\Livre;
use App\Entity\Auteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AjouterLivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, array('label' => 'Titre : ', 'attr' => array('placeholder' => "Saisir titre du livre", 'class' => 'form-control')))
            ->add('auteur', EntityType::class, array('class' => Auteur::class, 'choice_label' => 'pseudo', 'query_builder' => function(\App\Repository\AuteurRepository $repo) { return $repo->orderByAuteur();}, 'label' => 'Auteur : ', 'attr' => array('class' => 'form-control')))
            ->add('stock', NumberType::class, array('label' => 'Stock : ', 'attr' => array('placeholder' => "Saisir nombre de livres en stock", 'class' => 'form-control')))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
