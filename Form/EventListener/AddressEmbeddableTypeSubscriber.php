<?php

namespace Daften\Bundle\AddressingBundle\Form\EventListener;

use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepositoryInterface;
use CommerceGuys\Intl\Country\CountryRepository;
use CommerceGuys\Intl\Country\CountryRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

class AddressEmbeddableTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @var CountryRepositoryInterface
     */
    protected $countryRepository;

    /**
     * @var AddressFormatRepositoryInterface
     */
    protected $addressFormatRepository;

    /**
     * @var SubdivisionRepositoryInterface
     */
    protected $subdivisionRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param CountryRepositoryInterface $countryRepository
     * @param FormFactoryInterface $factory
     */
    public function __construct(
        FormFactoryInterface $factory,
        CountryRepositoryInterface $countryRepository,
        AddressFormatRepositoryInterface $addressFormatRepository,
        SubdivisionRepositoryInterface $subdivisionRepository
    ) {
        $this->formFactory = $factory;
        $this->countryRepository = $countryRepository;
        $this->addressFormatRepository = $addressFormatRepository;
        $this->subdivisionRepository = $subdivisionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event): void
    {
        /** @var AddressEmbeddable $address */
        $address = $event->getData();
        if (null === $address) {
            return;
        }

        $countryCode = $address->getCountryCode();
        if (null === $countryCode) {
            return;
        }

        // Get the address format for Country.
        $addressFormat = $this->addressFormatRepository->get($countryCode);

        $form = $event->getForm();

        $form
            ->add('recipient')
            ->add('organization')
            ->add('addressLine1')
            ->add('addressLine2')
            ->add('locality')
            ->add('dependentLocality')
            ->add('administrativeArea')
            ->add('sortingCode')
        ;
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('countryCode', $data)) {
            return;
        }

        if ('' === $data['countryCode']) {
            return;
        }

        // Get the address format for Country.
        $addressFormat = $this->addressFormatRepository->get($countryCode);

        $form = $event->getForm();

        $form
            ->add('recipient')
            ->add('organization')
            ->add('addressLine1')
            ->add('addressLine2')
            ->add('locality')
            ->add('dependentLocality')
            ->add('administrativeArea')
            ->add('sortingCode')
        ;
    }
}