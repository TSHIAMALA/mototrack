<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\Pcode;
use App\Repository\PcodeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commune', EntityType::class, [
                'class' => Pcode::class,
                'choice_label' => function (Pcode $pcode) {
                    return $pcode->getCitizenName() ?? $pcode->getLabel();
                },
                'query_builder' => function (PcodeRepository $repo) {
                    return $repo->createQueryBuilder('p')
                        ->join('p.pcodeCategory', 'pc')
                        ->where('pc.level = :level')
                        ->andWhere('p.isActive = true')
                        ->setParameter('level', 4)
                        ->orderBy('p.label', 'ASC');
                },
                'label' => 'Commune',
                'required' => false,
                'placeholder' => 'Sélectionner une commune',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('quartier', TextType::class, [
                'label' => 'Quartier',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Masina'],
            ])
            ->add('avenue', TextType::class, [
                'label' => 'Avenue',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Avenue de la Paix'],
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 123'],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Adresse principale',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Adresse::class]);
    }
}
