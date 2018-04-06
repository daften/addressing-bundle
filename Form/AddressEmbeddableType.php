<?php

namespace Daften\Bundle\AddressingBundle\Form;

use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\CountryRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form used to have an Embeddable Address form.
 */
class AddressEmbeddableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('countryCode', CountryType::class)
        ;

        $formModifier = function (FormInterface $form, $countryCode) {
            $addressFormatRepository = new AddressFormatRepository();
            $countryRepository = new CountryRepository();

            // Get the address format for Country.
            $addressFormat = $addressFormatRepository->get($countryCode);

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
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getCountryCode());
            }
        );

        $builder->get('countryCode')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $countryCode = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $countryCode);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddressEmbeddable::class,
        ]);
    }
}
