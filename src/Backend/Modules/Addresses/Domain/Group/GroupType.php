<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Backend\Modules\Addresses\Domain\Address\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //-- Add image field
        $builder
            ->add('image', FileType::class, array(
                'label' => 'lbl.Image',
                'required' => false
            ))
            ->add('translations', CollectionType::class, array(
                'entry_type' => GroupTranslationType::class,
            ))
            ->add('addresses', EntityType::class, array(
                'class' => Address::class,
                'label' => 'lbl.Addresses',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => GroupDataTransferObject::class]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'addresses_group';
    }
}
