# The Addressing Bundle

[![Build Status](https://travis-ci.org/daften/addressing-bundle.svg?branch=develop)](https://travis-ci.org/daften/addressing-bundle)
[![Maintainability](https://api.codeclimate.com/v1/badges/c8d0411c6ae51c1f1119/maintainability)](https://codeclimate.com/github/daften/addressing-bundle/maintainability)

## Installation

Don't forget to add the mapping to your doctrine.yaml file:
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