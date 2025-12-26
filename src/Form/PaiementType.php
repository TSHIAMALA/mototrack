<?php

namespace App\Form;

use App\Entity\Moto;
use App\Entity\Paiement;
use App\Entity\Taxe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moto', EntityType::class, [
                'class' => Moto::class,
                'choice_label' => function (Moto $moto) {
                    return $moto->getMarque() . ' ' . $moto->getModele() . ' - ' . $moto->getNumeroChassis();
                },
                'label' => 'Moto',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('taxe', EntityType::class, [
                'class' => Taxe::class,
                'choice_label' => function (Taxe $taxe) {
                    return $taxe->getLibelle() . ' - ' . $taxe->getMontant() . ' $';
                },
                'label' => 'Taxe',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('montant', MoneyType::class, ['label' => 'Montant', 'currency' => 'USD', 'attr' => ['class' => 'form-control']])
            ->add('modePaiement', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => ['Cash' => 'CASH', 'Mobile Money' => 'MOBILE_MONEY', 'Virement' => 'VIREMENT'],
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Paiement::class, 'user' => null]);
    }
}
