<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\Livre;
use App\Entity\Auteur;
use App\Entity\Salon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AjouterParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('livre', EntityType::class, array('class' => Livre::class, 'choice_label' => 'titre', 'query_builder' => function(\App\Repository\LivreRepository $repo) use ($id) { return $repo->byAuteurOrderByTitre($id);}, 'label' => 'Livre présenté : ', 'attr' => array('class' => 'form-control')))
            ->add('annuler', ResetType::class, array('label' => 'Reset', 'attr' =>array('class' => 'btn btn-danger btn-lg')))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success btn-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'id' => null
        ]);
    }
}
