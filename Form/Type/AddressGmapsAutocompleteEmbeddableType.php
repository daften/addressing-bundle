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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form used to have an Embeddable Address form with autocomplete with Gmaps.
 */
class AddressGmapsAutocompleteEmbeddableType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('addressAutocomplete', TextType::class, [
                'mapped' => false,
                'label' => 'Address',
                'attr' => [
                    'class' => 'address-autocomplete-input',
                    'data-allowed-countries' => implode('|', $options['allowed_countries']), // TODO
                    'data-api-key' => 'test', // @TODO
                ],
            ])
            ->add('countryCode', HiddenType::class)
            ->add('addressLine1', HiddenType::class)
            ->add('addressLine2', HiddenType::class)
            ->add('postalCode', HiddenType::class)
            ->add('locality', HiddenType::class)
            ->add('dependentLocality', HiddenType::class)
            ->add('administrativeArea', HiddenType::class)
        ;

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function(FormEvent $event){
                $address = $event->getData();
                $form = $event->getForm();

                if ($address) {
                    $form->get('addressAutocomplete')->setData($address);
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => AddressEmbeddable::class,
            'allowed_countries' => [],
        ]);

        $resolver->setAllowedTypes('allowed_countries', ['null', 'string[]']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'daften_address_embeddable';
    }
}
