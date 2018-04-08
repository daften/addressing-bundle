<?php

namespace Daften\Bundle\AddressingBundle\Form\Type;

use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\CountryRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form used to have an Embeddable Address form.
 */
class AddressEmbeddableType extends AbstractType
{
    /**
     * @var EventSubscriberInterface
     */
    private $addressEmbeddableTypeSubscriber;

    /**
     * @param string $dataClass
     * @param string[] $validationGroups
     * @param EventSubscriberInterface $buildAddressFormSubscriber
     */
    public function __construct(EventSubscriberInterface $addressEmbeddableTypeSubscriber)
    {
        $this->addressEmbeddableTypeSubscriber = $addressEmbeddableTypeSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('countryCode', CountryType::class)
            ->addEventSubscriber($this->addressEmbeddableTypeSubscriber)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => AddressEmbeddable::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'daften_address_embeddable';
    }
}
