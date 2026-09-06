<?php

namespace App\Form;

use App\Entity\StockItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('finalPayPrice')
            ->add('finalSellPrice')
            ->add('isBought')
            ->add('isSold')
            ->add('isBuyable')
            ->add('item')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockItem::class,
        ]);
    }
}
