<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;
use Backend\Modules\Addresses\Form\SettingsType;
use Common\ModulesSettings;

/**
 * Class Settings
 *
 * @package \Backend\Modules\Addresses\Actions
 */
class Settings extends ActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $form = $this->createForm(SettingsType::class);

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->template->assign('form', $form->createView());

            $this->parse();
            $this->display();
            return;
        }
        
        $data = $form->getData();

        /** @var ModulesSettings $modulesSettings */
        $modulesSettings = $this->get('fork.settings');
        foreach ($data as $setting => $value) {
            $modulesSettings->set($this->getModule(), $setting, $value);
        }

        $this->redirect(Model::createURLForAction('Settings', null, null, array('report' => 'updated')));
    }
}