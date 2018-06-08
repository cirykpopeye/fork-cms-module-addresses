<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //-- Add image field
        $builder
            ->add('groups', EntityType::class, array(
                'class' => Group::class,
                'label' => 'lbl.Groups',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('translations', CollectionType::class, array(
                'entry_type' => AddressTranslationType::class
            ))
            ->add('mediaGroup', MediaGroupType::class, ['label' => 'lbl.Images', 'required' => false])
            ->add('firstName', TextType::class, array('required' => false, 'label' => 'lbl.FirstName'))
            ->add('lastName', TextType::class, array('required' => false, 'label' => 'lbl.LastName'))
            ->add('email', TextType::class, array('required' => false, 'label' => 'lbl.Email'))
            ->add('street', TextType::class, array('label' => 'lbl.Street'))
            ->add('number', TextType::class, array('label' => 'lbl.Number'))
            ->add('postal', TextType::class, array('label' => 'lbl.Postal'))
            ->add('city', TextType::class, array('label' => 'lbl.City'))
            ->add('country', CountryType::class, array('label' => 'lbl.Country'))
            ->add('telephone', TextType::class, array('required' => false, 'label' => 'lbl.Telephone'))
            ->add('fax', TextType::class, array('required' => false, 'label' => 'lbl.Fax'))
            ->add('website', TextType::class, array('required' => false, 'label' => 'lbl.Website'))
            ->add('btw', TextType::class, array('required' => false, 'label' => 'lbl.Btw'))
            ->add('note', TextType::class, array('required' => false, 'label' => 'lbl.Note'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => AddressDataTransferObject::class]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'addresses_address';
    }
}
