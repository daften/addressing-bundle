<?php

namespace Daften\Bundle\AddressingBundle\Twig;

use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Daften\Bundle\AddressingBundle\Service\AddressOutputService;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AddressExtension extends AbstractExtension
{

    /**
     * @var AddressOutputService
     */
    protected $addressOutputService;

    /**
     * AddressExtension constructor.
     *
     * @param AddressOutputService $addressOutputService
     *   The address output service.
     */
    public function __construct(AddressOutputService $addressOutputService)
    {
        $this->addressOutputService = $addressOutputService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('address_plain', [$this, 'addressPlain'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * Renders the address as an address plain.
     */
    public function addressPlain(Environment $env, AddressEmbeddable $addressEmbeddable): string
    {
        return $env->render(
            '@Addressing/address-plain.html.twig',
            $this->addressOutputService->getAddressPlain($addressEmbeddable)
        );
    }

}
