<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Settings\Notification;

use XLite\Model\DTO\Base\CommonCell;

class Admin extends ANotification
{
    /**
     * @param \XLite\Model\Notification $object
     */
    protected function init($object)
    {
        parent::init($object);

        $this->default->page = 'admin';

        $settings = [
            'status'    => $object->getEnabledForAdmin(),
            'available' => $object->getAvailableForAdmin(),
            'subject'   => $object->getAdminSubject(),
        ];
        $this->settings = new CommonCell($settings);

        $this->scheme->header = array_replace($this->scheme->header, ['status' => $object->getAdminHeaderEnabled()]);
        $this->scheme->greeting = array_replace($this->scheme->header, ['status' => $object->getAdminGreetingEnabled()]);
        $this->scheme->text = $object->getAdminText();
        $this->scheme->signature = array_replace($this->scheme->header, ['status' => $object->getAdminSignatureEnabled()]);
    }

    /**
     * @param \XLite\Model\Notification $object
     * @param array|null                $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        if ($object->getAvailableForAdmin() || $object->getEnabledForAdmin()) {
            $object->setEnabledForAdmin($this->settings->status);
            $object->setAdminSubject($this->settings->subject);

            $object->setAdminHeaderEnabled($this->scheme->header['status']);
            $object->setAdminGreetingEnabled($this->scheme->greeting['status']);
            $object->setAdminText(isset($rawData['scheme']['text']) ? $rawData['scheme']['text'] : $this->scheme->text);
            $object->setAdminSignatureEnabled($this->scheme->signature['status']);
        }
    }

    /**
     * @param \XLite\Model\Notification $object
     *
     * @return string
     */
    protected function getBodyPath($object)
    {
        $layout = \XLite\Core\Layout::getInstance();

        $baseSkin = $layout->getSkin();
        $baseInterface = $layout->getInterface();
        $baseInnerInterface = $layout->getInnerInterface();

        $layout->setMailSkin(\XLite::ADMIN_INTERFACE);

        $path = $layout->getResourceFullPath($object->getTemplatesDirectory() . '/body.twig');

        // restore old skin
        switch ($baseInterface) {
            default:
            case \XLite::ADMIN_INTERFACE:
                $layout->setAdminSkin();
                break;

            case \XLite::CUSTOMER_INTERFACE:
                $layout->setCustomerSkin();
                break;

            case \XLite::CONSOLE_INTERFACE:
                $layout->setConsoleSkin();
                break;

            case \XLite::MAIL_INTERFACE:
                $layout->setMailSkin($baseInnerInterface);
                break;
        }

        $layout->setSkin($baseSkin);

        return $path;
    }
}
