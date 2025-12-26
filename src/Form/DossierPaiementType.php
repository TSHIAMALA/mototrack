<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\Moto;
use App\Entity\Paiement;
use App\Entity\Taxe;
use App\Repository\MotoRepository;
use App\Repository\TaxeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierPaiementType extends AbstractType
{
    public function __construct(private TaxeRepository $taxeRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('moto', EntityType::class, [
                'class' => Moto::class,
                'choice_label' => function (Moto $moto) {
                    return sprintf('%s - %s (%s)', 
                        $moto->getMotard()?->getNomComplet() ?? 'N/A',
                        $moto->getNumeroChassis(),
                        $moto->getMarque()
                    );
                },
                'query_builder' => function (MotoRepository $repo) use ($user) {
                    $qb = $repo->createQueryBuilder('m')
                        ->leftJoin('m.motard', 'mo')
                        ->orderBy('mo.nomComplet', 'ASC');
                    return $qb;
                },
                'label' => 'Moto',
                'placeholder' => 'Sélectionner une moto',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('taxes', EntityType::class, [
                'class' => Taxe::class,
                'choice_label' => function (Taxe $taxe) {
                    return sprintf('%s - %s FC', $taxe->getLibelle(), number_format((float)$taxe->getMontant(), 0, ',', ' '));
                },
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'label' => 'Taxes à payer',
                'attr' => ['class' => 'space-y-2'],
            ])
            ->add('modePaiement', ChoiceType::class, [
                'choices' => [
                    'Espèces' => Paiement::MODE_CASH,
                    'Mobile Money' => Paiement::MODE_MOBILE_MONEY,
                    'Virement' => Paiement::MODE_VIREMENT,
                ],
                'label' => 'Mode de paiement',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
            'user' => null,
        ]);
    }
}
