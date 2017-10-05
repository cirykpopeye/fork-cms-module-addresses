<?php

namespace Backend\Modules\Addresses\Form;

use Backend\Core\Engine\Model;
use Common\ModulesSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SettingsType
 *
 * @package \Backend\Modules\Addresses\Form
 */
class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ModulesSettings $forkSettings */
        $forkSettings = Model::get('fork.settings');
        $module = 'Addresses';

        $checkBoxes = array(
            array('key' => 'sortBySequence', 'default' => false)
        );

        foreach ($checkBoxes as $checkBox) {
            $builder->add($checkBox['key'], CheckboxType::class, array('required' => false, 'data' => $forkSettings->get($module, $checkBox['key'], $checkBox['default'])));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }
}