<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PlaylistsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom de la playlist',
                'required' => true
            ])
            ->add('description', null, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 15]
            ])
            ->add('formations', EntityType::class, [
                'class' => Formation::class,
                'label' => 'Vidéos de la playlist',
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'disabled' => true,
                'attr' => ['rows' => 5]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
