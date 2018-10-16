# The Addressing Bundle

[![Build Status](https://travis-ci.org/daften/addressing-bundle.svg?branch=develop)](https://travis-ci.org/daften/addressing-bundle)
[![Maintainability](https://api.codeclimate.com/v1/badges/c8d0411c6ae51c1f1119/maintainability)](https://codeclimate.com/github/daften/addressing-bundle/maintainability)

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

You'll also need to add some javascript code, to make sure the form changes on
changing the country code work.

The script below gives an example. entity_name is the snake_case formation of
the entity that contains the address field and has the address form on it's
form.

```javascript
var countryCodeChange = require('../../public/bundles/addressing/js/countryCodeChange');
countryCodeChange.initialize('<entity_name>');
```

## Usage

### Entity property

You need to add an address field as an ORM Embedded property.

```php
<?php

namespace App\Entity;

use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstallationAddressRepository")
 */
class AddressExample
{
    /**
     * @ORM\Embedded(class="Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable")
     */
    private $address;

    /**
     * AddressExample constructor.
     */
    public function __construct()
    {
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

An example form for the AddressExample class given above.

```php
<?php

namespace App\Form;

use App\Entity\AddressExample;
use Daften\Bundle\AddressingBundle\Form\Type\AddressEmbeddableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InstallationAddressType
 */
class InstallationAddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressEmbeddableType::class);
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