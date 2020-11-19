<?php

namespace App\Form;

use App\Entity\BonDeDepot;
use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AjouterBonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('livre', EntityType::class, array('class' => Livre::class, 'choice_label' => 'titre', 'query_builder' => function(\App\Repository\LivreRepository $repo) { return $repo->orderByTitre();}, 'label' => 'Livre : ', 'attr' => array('class' => 'form-control')))
            ->add('nbEnvoyes', NumberType::class, array('label' => 'Nombre de livres envoyés : ', 'attr' => array('placeholder' => "Saisir nombre de livres envoyés", 'class' => 'form-control')))
            ->add('destinataire', TextType::class, array('label' => 'Destinataire : ', 'attr' => array('placeholder' => "Saisir destinataire du bon de dépôt", 'class' => 'form-control')))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BonDeDepot::class,
        ]);
    }
}
