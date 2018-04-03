<?php

namespace Daften\Bundle\AddressingBundle\Form;

use Daften\Bundle\AddressingBundle\Embeddable\AddressEmbeddable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form used to have an Embeddable Address form.
 */
class AddressEmbeddableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('countryCode')
            ->add('administrativeArea')
            ->add('locality')
            ->add('dependentLocality')
            ->add('sortingCode')
            ->add('addressLine1')
            ->add('addressLine2')
            ->add('organization')
            ->add('recipient')
            ->add('locale')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddressEmbeddable::class,
        ]);
    }
}
