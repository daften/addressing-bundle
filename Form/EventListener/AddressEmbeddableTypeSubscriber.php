<?php

namespace Daften\Bundle\AddressingBundle\Form\EventListener;

use CommerceGuys\Addressing\AddressFormat\AddressFormatHelper;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\AddressFormat\FieldOverrides;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Daften\Bundle\AddressingBundle\Validator\Constraints\EmbeddedAddressFormatConstraint;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;
use Symfony\Component\Validator\Mapping\PropertyMetadataInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var ?ValidatorInterface
     */
    private $validator;

    /**
     * @param CountryRepositoryInterface $countryRepository
     * @param FormFactoryInterface $factory
     */
    public function __construct(
        FormFactoryInterface $factory,
        CountryRepositoryInterface $countryRepository,
        AddressFormatRepositoryInterface $addressFormatRepository,
        SubdivisionRepositoryInterface $subdivisionRepository,
        ValidatorInterface $validator = null
    ) {
        $this->formFactory = $factory;
        $this->countryRepository = $countryRepository;
        $this->addressFormatRepository = $addressFormatRepository;
        $this->subdivisionRepository = $subdivisionRepository;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event): void
    {
        /** @var AddressEmbeddable $address */
        $address = $event->getData();
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();
        $autocompleteOff = (!isset($options['attr']['autocomplete']) || $options['attr']['autocomplete'] === 'off');
        $element_options = [];

        if (null === $address) {
            // No address set yet, let's set the country code to the default.
            $address = new AddressEmbeddable($options['default_country']);
            $event->setData($address);
        }

        $countryCode = isset($address) ? $address->getCountryCode() : $options['default_country'];
        if (null === $countryCode) {
            return;
        }

        // Get the address format for Country.
        $addressFormat = $this->addressFormatRepository->get($countryCode);

        $form = $event->getForm();

        if ($autocompleteOff) {
            $element_options = [
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ];
        }

        $fieldOverrides = $this->getFieldOverrides($form);
        $requiredFields = AddressFormatHelper::getRequiredFields($addressFormat, $fieldOverrides);

        foreach (AddressFormatHelper::getGroupedFields($addressFormat->getFormat(), $fieldOverrides) as $line_index => $line_fields) {
            foreach ($line_fields as $field_index => $field) {
                $element_options['required'] = false;
                if (in_array($field, $requiredFields)) {
                    $element_options['required'] = true;
                }

                $form->add(
                    $field,
                    null,
                    $element_options
                );
            }
        }

        $unused_fields = array_diff(AddressField::getAll(), $addressFormat->getUsedFields());
        foreach ($unused_fields as $field) {
            $form->remove($field);
        }
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

        // Remove all form fields first, since they were already set in the pre_set_data for the default country.
        $all_fields = AddressField::getAll();
        foreach ($all_fields as $field) {
            $form->remove($field);
        }

        foreach (AddressFormatHelper::getGroupedFields($addressFormat->getFormat(), $this->getFieldOverrides($form)) as $line_index => $line_fields) {
            foreach ($line_fields as $field_index => $field) {
                $form->add($field);
            }
        }

        $unused_fields = array_diff(AddressField::getAll(), $addressFormat->getUsedFields());
        foreach ($unused_fields as $field) {
            $form->remove($field);
        }

        if ($form->getData() !== $data) {
            $addressEmbeddable = new AddressEmbeddable();
            foreach ($data as $field => $value) {
                $method = 'set'.ucfirst($field);
                if (method_exists($addressEmbeddable, $method)) {
                    $addressEmbeddable->{$method}($value);
                }
            }
            $form->setData($addressEmbeddable);
        }
    }

    private function getFieldOverrides(FormInterface $form): FieldOverrides
    {
        if (!$this->validator) {
            return new FieldOverrides([]);
        }

        $formParent = $form->getParent();
        if (!$formParent) {
            return new FieldOverrides([]);
        }

        $parentEntity = $formParent->getData();
        if (!is_object($parentEntity)) {
            return new FieldOverrides([]);
        }

        try {
            $metadata = $this->validator->getMetadataFor(get_class($parentEntity));
        } catch (NoSuchMetadataException $e) {
            return new FieldOverrides([]);
        }

        $propertyMetadatas = $metadata->getPropertyMetadata($form->getName());
        /** @var PropertyMetadataInterface $propertyMetadata */
        foreach ($propertyMetadatas as $propertyMetadata) {
            $constraints = $propertyMetadata->getConstraints();
            foreach ($constraints as $constraint) {
                if ($constraint instanceof EmbeddedAddressFormatConstraint) {
                    return $constraint->fieldOverrides;
                }
            }
        }

        return new FieldOverrides([]);
    }
}
