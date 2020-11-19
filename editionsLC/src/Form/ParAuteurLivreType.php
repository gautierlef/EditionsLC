<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ParAuteurLivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('livre', EntityType::class, array('class' => Livre::class, 'choice_label' => 'titre', 'query_builder' => function(\App\Repository\LivreRepository $repo) use ($id) { return $repo->byAuteurOrderByTitre($id);}, 'label' => 'Livre : ', 'attr' => array('class' => 'form-control')))
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
