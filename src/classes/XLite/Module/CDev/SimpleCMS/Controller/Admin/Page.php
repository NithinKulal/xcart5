<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Controller\Admin;

/**
 * Page controller
 *
 */
class Page extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'id');

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage custom pages');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $model = $id
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->find($id)
            : null;

        return ($model && $model->getId())
            ? static::t('Edit page')
            : static::t('New page');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
        if (!\XLite\Core\Request::getInstance()->id) {
            $this->setReturnURL(
                $this->buildURL(
                    'page',
                    '',
                    array('id' => $this->getModelForm()->getModelObject()->getId())
                )
            );
        }
    }

    /**
     * Update model and close page
     *
     * @return void
     */
    protected function doActionUpdateAndClose()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(
                \XLite\Core\Converter::buildURL('pages')
            );
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\SimpleCMS\View\Model\Page';
    }

}
