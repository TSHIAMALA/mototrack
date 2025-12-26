<?php

namespace App\Form;

use App\Entity\Motard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => ['Personne physique' => 'PHYSIQUE', 'Personne morale' => 'MORALE'],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('nomComplet', TextType::class, ['label' => 'Nom complet', 'attr' => ['class' => 'form-control']])
            ->add('identification', TextType::class, ['label' => 'Identification', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('telephone', TelType::class, ['label' => 'Téléphone', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('adresses', CollectionType::class, [
                'entry_type' => AdresseType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Adresses',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Motard::class]);
    }
}
