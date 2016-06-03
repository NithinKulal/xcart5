<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View\Model\Profile;

/**
 * \XLite\View\Model\Profile\AdminMain
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain implements \XLite\Base\IDecorator
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);
        $profile = $this->getModelObject();

        if ($profile && $profile->getPictureUrl()) {
            $this->summarySchema['picture'] = array(
                self::SCHEMA_CLASS    => '\XLite\Module\CDev\SocialLogin\View\FormField\ProfileImage',
                self::SCHEMA_LABEL    => 'Profile image',
                self::SCHEMA_REQUIRED => false,
                \XLite\Module\CDev\SocialLogin\View\FormField\ProfileImage::PARAM_URL      => $profile->getPictureUrl(),
            );
        }
    }
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if ($this->getModelObject()->isSocialProfile()) {
            unset($this->mainSchema['password']);
            unset($this->mainSchema['password_conf']);
        }

        return parent::getFormFieldsForSectionMain();
    }
}
