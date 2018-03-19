<?php

namespace Daften\Addressing\Form;

use Daften\Addressing\Embeddable\AddressEmbeddable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressEmbeddableType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddressEmbeddable::class,
        ]);
    }
}
