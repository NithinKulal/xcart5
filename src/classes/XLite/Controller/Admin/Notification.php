<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Notification controller
 */
class Notification extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['page', 'templatesDirectory']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $notification = $this->getNotification();

        return $notification
            ? $notification->getName()
            : '';
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        $notification = $this->getNotification();

        if ($notification->getAvailableForCustomer() || $notification->getEnabledForCustomer()) {
            $list['customer'] = static::t('notification.section.customer');
        }

        if ($notification->getAvailableForAdmin() || $notification->getEnabledForAdmin()) {
            $list['admin'] = static::t('notification.section.administrator');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['customer'] = 'notification/body.twig';
        $list['admin'] = 'notification/body.twig';

        return $list;
    }

    /**
     * Returns description of current notification
     *
     * @return string
     */
    public function getDescription()
    {
        $notification = $this->getNotification();

        return $notification
            ? $notification->getDescription()
            : '';
    }

    /**
     * Returns object to get initial data and populate submitted data to
     *
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        $class = $this->getFormModelObjectClass();

        return new $class($this->getNotification());
    }

    /**
     * @return \XLite\Model\DTO\Base\ADTO
     */
    protected function getFormModelObjectClass()
    {
        return 'admin' === $this->getPage()
            ? 'XLite\Model\DTO\Settings\Notification\Admin'
            : 'XLite\Model\DTO\Settings\Notification\Customer';
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $dto = $this->getFormModelObject();
        $formModel = new \XLite\View\FormModel\Settings\Notification\Notification(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($this->getNotification(), $rawData[$this->formName]);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo('The notification has been updated');

        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);
        }
    }

    /**
     * Returns notification
     *
     * @return \XLite\Model\Notification
     */
    protected function getNotification()
    {
        $id = \XLite\Core\Request::getInstance()->templatesDirectory;

        return $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id)
            : null;
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getNotification();
    }
}
