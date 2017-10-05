<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Backend\Form\Type\EditorType;
use Backend\Form\Type\MetaType;
use Backend\Modules\Addresses\Domain\Address\Command\UpdateAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressTranslationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //-- Add title
        $builder->add('title', TextType::class, array(
            'attr' => array('class' => 'item-title'),
            'label' => 'lbl.Title',
            'required' => true
        ));

        $builder->add('description', EditorType::class, array(
            'label' => 'lbl.Description',
            'required' => false
        ));

        $builder
            ->add('summary', EditorType::class, array(
            'label' => 'lbl.Summary',
            'required' => false
            ))
            ->add('company', TextType::class, array(
                'required' => false,
                'label' => 'lbl.Company'
            ));

        $builder->add('titleShort', TextType::class, array(
            'label' => 'lbl.TitleShort',
            'required' => false
        ));

        $builder->add('actionFrom', DateType::class, array(
            'label' => 'lbl.ActionFrom',
            'required' => false
        ));
        $builder->add('actionTill', DateType::class, array(
            'label' => 'lbl.ActionTill',
            'required' => false
        ));
        $builder->add('actionMessage', EditorType::class, array(
            'label' => 'lbl.ActionMessage',
            'required' => false
        ));




        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $addressFormData = $event->getForm()->getParent()->getParent()->getData();
                    $event->getForm()->add(
                        'meta',
                        MetaType::class,
                        [
                            'base_field_name' => 'title',
                            'detail_url' => '',
                            'generate_url_callback_class' => 'addresses.repository.group_translation',
                            'generate_url_callback_method' => 'getURL',
                            'generate_url_callback_parameters' => [
                                $event->getData()->getLocale(),
                                $addressFormData instanceof UpdateAddress
                                    ? $addressFormData->getAddress()->getId() : null,
                            ],
                        ]
                    );
                }
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => AddressTranslationDataTransferObject::class]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'addressTranslation';
    }
}
