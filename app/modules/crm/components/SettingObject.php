<?php namespace modules\crm\components;

// "Keep the essence of your code, code isn't just a code, it's an art." -- Rifan Firdhaus Widigdo
use modules\core\components\BaseSettingObject;

/**
 * @author Rifan Firdhaus Widigdo <rifanfirdhaus@gmail.com>
 */
class SettingObject extends BaseSettingObject
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        switch ($this->renderer->section) {
            case 'crm':
                $this->initCrmSection();
                break;
        }
    }

    public function initCrmSection()
    {
        $this->renderer->view->on('block:core/admin/setting/index:begin', function () {
            echo $this->renderer->view->render('@modules/crm/views/admin/setting/menu', ['active' => 'general']);
        });
    }
}