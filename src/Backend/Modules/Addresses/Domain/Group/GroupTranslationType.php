<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Backend\Form\Type\EditorType;
use Backend\Form\Type\MetaType;
use Backend\Modules\Addresses\Domain\Group\Command\UpdateGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupTranslationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //-- Add title
        $builder->add('title', TextType::class, array(
            'attr' => array('class' => 'category-title'),
            'label' => 'lbl.Title',
            'required' => true
        ));
        $builder->add('description', EditorType::class, array(
            'label' => 'lbl.Description',
            'required' => false
        ));

        $builder->add('summary', EditorType::class, array(
            'label' => 'lbl.Summary',
            'required' => false
        ));

        $builder->add('hidden', CheckboxType::class, array(
            'label' => 'lbl.Hidden',
            'required' => false
        ));

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $groupFormData = $event->getForm()->getParent()->getParent()->getData();
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
                                $groupFormData instanceof UpdateGroup
                                    ? $groupFormData->getGroup()->getId() : null,
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
        $resolver->setDefaults(['data_class' => GroupTranslationDataTransferObject::class]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'groupTranslation';
    }
}
