<?php

namespace Daften\Bundle\AddressingBundle\Form\EventListener;

use CommerceGuys\Addressing\Enum\AddressField;
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
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('countryCode', $data)) {
            return;
        }

        if ('' === $data['countryCode']) {
            return;
        }

        // Get the address format for Country.
        $addressFormat = $this->addressFormatRepository->get($data['countryCode']);

        $form = $event->getForm();

        foreach ($addressFormat->getGroupedFields() as $line_index => $line_fields) {
            foreach ($line_fields as $field_index => $field) {
                $form->add($field);
            }
        }

        $unused_fields = array_diff(AddressField::getAll(), $addressFormat->getUsedFields());
        foreach ($unused_fields as $field) {
            $form->remove($field);
        }
    }
}
