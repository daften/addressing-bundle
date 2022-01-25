<?php

namespace Daften\Bundle\AddressingBundle\Form\Type;

use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\CountryRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\IntlCallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\Options;
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

        $countryCodeOptions = [];
        // When upgrading the min requirements to Symfony 5.1, use https://symfony.com/doc/current/reference/forms/types/choice.html#choice-filter
        if (null !== $options['allowed_countries'] && count($options['allowed_countries']) > 0) {
            $countryCodeOptions['choices'] = $options['allowed_countries'];
            $countryCodeOptions['choice_loader'] = null;
        }
        $countryCodeOptions['preferred_choices'] = $options['preferred_countries'];
        $countryCodeOptions['choice_translation_domain'] = $options['choice_translation_domain'];
        $builder
            ->add('countryCode', CountryType::class, $countryCodeOptions);

        $builder->addEventSubscriber($this->addressEmbeddableTypeSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => AddressEmbeddable::class,
            'attr' => [
                'class' => 'address-embeddable',
            ],
            'allowed_countries' => [], // After updating to Symfony 5.1, make this a filter list based on country code.
            'preferred_countries' => [],
            'default_country' => null,
            'choice_translation_domain' => false,
        ]);

        $resolver->setAllowedTypes('allowed_countries', ['null', 'string[]']);
        $resolver->setAllowedTypes('preferred_countries', ['null', 'string[]']);
        $resolver->setAllowedTypes('default_country', ['null', 'string']);
        $resolver->setAllowedTypes('choice_translation_domain', ['null', 'string', 'boolean']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'daften_address_embeddable';
    }
}
