<?php

namespace Daften\Bundle\AddressingBundle\Form\Type;

use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Repository\CountryRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepositoryInterface;
use CommerceGuys\Intl\Country\CountryRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Daften\Bundle\AddressingBundle\Service\AddressOutputService;
use Daften\Bundle\AddressingBundle\Service\GmapsAutocompleteService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form used to have an Embeddable Address form with autocomplete with Gmaps.
 */
class AddressEmbeddableGmapsAutocompleteType extends AddressEmbeddableType
{

    /**
     * @var GmapsAutocompleteService
     */
    protected $gmapsAutocompleteService;

    /**
     * @var AddressOutputService
     */
    protected $addressOutputService;

    public function __construct(
        GmapsAutocompleteService $gmapsAutocompleteService,
        AddressOutputService $addressOutputService,
        EventSubscriberInterface $addressEmbeddableTypeSubscriber
    ) {
        parent::__construct($addressEmbeddableTypeSubscriber);
        $this->gmapsAutocompleteService = $gmapsAutocompleteService;
        $this->addressOutputService = $addressOutputService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressAutocomplete', TextType::class, [
                'mapped' => false,
                'label' => 'addressAutocomplete',
                'help' => 'This field is used to search an address on Google and fill it out below. This could override other values!',
                'attr' => [
                    'class' => 'address-autocomplete-input',
                    'data-language' => $this->gmapsAutocompleteService->getLocale(),
                    'data-allowed-countries' => implode('|', $options['allowed_countries']),
                    'data-api-key' => $this->gmapsAutocompleteService->getGmapsApiKey(),
                ],
            ]);
        parent::buildForm($builder, $options);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event){
                $address = $event->getData();
                $form = $event->getForm();

                if ($address) {
                    $address_default = $this->addressOutputService->getAddressInline($address);
                    $form->get('addressAutocomplete')->setData($address_default);
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
            'gmaps_api_key' => '',
        ]);

        $resolver->setAllowedTypes('allowed_countries', ['null', 'string[]']);
        $resolver->setAllowedTypes('gmaps_api_key', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'daften_address_embeddable';
    }
}
