<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Theme tweaker template controller
 */
class ThemeTweakerTemplate extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'id', 'template');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Look & Feel') . ' :: ' . $this->getTemplateLocalPath();
    }

    /**
     * Is create request
     *
     * @return boolean
     */
    public function isCreate()
    {
        return (bool) \XLite\Core\Request::getInstance()->template;
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {

            if (\Xlite\Core\Request::getInstance()->isCreate) {

                echo <<<HTML
<script type="text/javascript">window.opener.location.reload();window.close()</script>
HTML;
                exit;

            } else {
                $this->setReturnUrl(\XLite\Core\Converter::buildURL('theme_tweaker_templates'));
            }
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\ThemeTweaker\View\Model\Template';
    }

    /**
     * Returns current template short path
     *
     * @return string
     */
    protected function getTemplateLocalPath()
    {
        $localPath = '';

        if ($this->isCreate()) {
            $localPath = \XLite\Core\Request::getInstance()->template;
        } elseif (\XLite\Core\Request::getInstance()->id) {
            $template = \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')
                ->find(\XLite\Core\Request::getInstance()->id);

            $localPath = $template ? $template->getTemplate() : '';
        }

        return $localPath;
    }
}
