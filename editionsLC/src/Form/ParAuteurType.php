<?php

namespace App\Form;

use App\Entity\Auteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ParAuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('auteur', EntityType::class, array('class' => Auteur::class, 'choice_label' => 'pseudo', 'query_builder' => function(\App\Repository\AuteurRepository $repo) { return $repo->orderByAuteur();}, 'label' => 'Auteur : ', 'attr' => array('class' => 'form-control')))
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
