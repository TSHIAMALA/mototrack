<?php

namespace App\Form;

use App\Entity\MarqueMoto;
use App\Entity\Motard;
use App\Entity\Moto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motard', EntityType::class, [
                'class' => Motard::class,
                'choice_label' => 'nomComplet',
                'label' => 'Propriétaire',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('marque', EntityType::class, [
                'class' => MarqueMoto::class,
                'choice_label' => 'nom',
                'label' => 'Marque',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('modele', TextType::class, ['label' => 'Modèle', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('numeroChassis', TextType::class, ['label' => 'N° Châssis', 'attr' => ['class' => 'form-control']])
            ->add('couleur', TextType::class, ['label' => 'Couleur', 'required' => false, 'attr' => ['class' => 'form-control']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Moto::class]);
    }
}
