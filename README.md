# The Addressing Bundle

[![Build Status](https://travis-ci.org/daften/addressing-bundle.svg?branch=develop)](https://travis-ci.org/daften/addressing-bundle)
[![Maintainability](https://api.codeclimate.com/v1/badges/c8d0411c6ae51c1f1119/maintainability)](https://codeclimate.com/github/daften/addressing-bundle/maintainability)

## Requirements

* jQuery loaded as $
* jQuery Once loaded properly.

## Installation

Add the mapping to your doctrine.yaml file:

```yaml
doctrine:
    ...
    orm:
        ...
        entity_managers:
            default:
                ...
                mappings:
                    AddressingBundle:
                        is_bundle: true
```

TODO: Explain why this mapping is needed.

You'll also need to add some configuration or javascript depending on the form you'll use for address information. For
this you need to run `bin/console assets:install` to copy the bundle assets to the public folder.

### AddressEmbeddableType

You'll also need to add some javascript code, to make sure the form changes on
changing the country code work.

The script below gives an example. You just need to initialize the javascript functionality. All address fields will
automatically be covered. This only works when using Symfony 4 with Webpack Encore.

```javascript
var countryCodeChange = require('../../public/bundles/addressing/js/countryCodeChange');
countryCodeChange.initialize();
```

### AddressEmbeddableGmapsAutocompleteType

You'll also need to add some javascript code, to make sure the autocomplete functionality works.

The script below gives an example. You just need to initialize the javascript functionality. All autocomplete address
fields will automatically be covered. This only works when using Symfony 4 with Webpack Encore.

```javascript
var addressGmapsAutocomplete = require('../../public/bundles/addressing/js/addressGmapsAutocomplete');
addressGmapsAutocomplete.initialize();
```

You also need to add the Google API key to the .env file with property key GMAPS_API_KEY. You can override this by
overruling the service definition for daften.service.gmaps_autocomplete_service.

## Usage

### Entity property

You need to add an address field as an ORM Embedded property.

```php
<?php

namespace App\Entity;

use App\Repository\InstallationAddressRepository;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Daften\Bundle\AddressingBundle\Validator\Constraints as AddressingBundleAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallationAddressRepository::class)]
class AddressExample
{

    #[ORM\Embedded(class: AddressEmbeddable::class)]
    #[AddressingBundleAssert\EmbeddedAddressFormatConstraint(fields: [
        'addressLine1'
        'postalCode'
        'locality'
        'organization'
        'givenName'
        'familyName'
        'addressLine2'
        'additionalName'
        'administrativeArea'
        'dependentLocality'
        'sortingCode'
    ])]
    private AddressEmbeddable $address;

    /**
     * AddressExample constructor.
     */
    public function __construct()
    {
        $this->address = new AddressEmbeddable();
    }

    /**
     * @return AddressEmbeddable
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param AddressEmbeddable $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }
}
```

### Entity form

#### AddressEmbeddableType

There are 3 additional options that can be used for this form type:

* allowed_countries: The countries allowed in the country dropdown. An array where the keys should be the country name
  and the values should be the 2-character country code.
* preferred_countries: An array with the preferred countries, using the 2-character country codes.
* default_country: The default country to show in the country dropdown.

An example form for the AddressExample class given above using the default AddressEmbeddableType with separate fields.

```php
<?php

namespace App\Form;

use App\Entity\AddressExample;
use Daften\Bundle\AddressingBundle\Form\Type\AddressEmbeddableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressExampleType
 */
class AddressExampleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressEmbeddableType::class, [
                 'allowed_countries' => [
                     'United States' => 'US',
                     'United Kingdom' => 'UK',
                     'Belgium' => 'BE',
                 ],
                 'preferred_countries' => ['BE', 'US'],
                 'default_country' => 'US',
             ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddressExample::class,
        ]);
    }
}
```

#### AddressEmbeddableGmapsAutocompleteType

There is 1 additional option that can be used for this form type:

* allowed_countries: The countries allowed for autocompletion. An array where the values should be the 2-character
  country code.

An example form for the AddressExample class given above using the AddressEmbeddableGmapsAutocompleteType with one
autocomplete field.

```php
<?php

namespace App\Form;

use App\Entity\AddressExample;
use Daften\Bundle\AddressingBundle\Form\Type\AddressEmbeddableGmapsAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressExampleType2
 */
class AddressExampleType2 extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('address', AddressEmbeddableGmapsAutocompleteType::class, [
                'label' => 'address',
                'translation_domain' => 'address',
                'allowed_countries' => [
                    'BE',
                    'NL',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddressExample::class,
        ]);
    }
}
```
