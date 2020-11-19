<?php

namespace App\Form;

use App\Entity\Vente;
use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifierVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array('label' => 'Date : '))
            ->add('source', TextType::class, array('label' => 'Source : ', 'attr' => array('placeholder' => "Saisir source des ventes", 'class' => 'form-control')))
            ->add('nbVentes', NumberType::class, array('label' => 'Nombre de ventes : ', 'attr' => array('placeholder' => "Saisir nombre de ventes", 'class' => 'form-control')))
            ->add('prix', MoneyType::class, array('label' => 'Prix : ', 'attr' => array('placeholder' => "Saisir prix de vente", 'class' => 'form-control')))
            ->add('livre', EntityType::class, array('class' => Livre::class, 'choice_label' => 'titre', 'query_builder' => function(\App\Repository\LivreRepository $repo) { return $repo->orderByTitre();}, 'label' => 'Livre : ', 'attr' => array('class' => 'form-control')))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}
