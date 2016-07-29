<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Notifications common page controller
 */
class NotificationCommon extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Email notifications');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $dto = $this->getFormModelObject();
        $formModel = new \XLite\View\FormModel\Settings\Notification\Common(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo(null, $rawData[$this->formName]);
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Translation::getInstance()->reset();

            \XLite\Core\TopMessage::addInfo('The common notification fields has been updated');

        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);
        }

        $this->setReturnURL($this->buildURL('notification_common'));
    }

    /**
     * Returns object to get initial data and populate submitted data to
     *
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        return new \XLite\Model\DTO\Settings\Notification\Common();
    }
}
