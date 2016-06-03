<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify;

/**
 * Edit language label
 */
class EditLabel extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_LABEL_ID = 'label_id';

    /**
     * Label (cache)
     *
     * @var \XLite\Model\LanguageLabel
     */
    protected $label = null;


    /**
     * Get label
     *
     * @return \XLite\Model\LanguageLabel|boolean
     */
    public function getLabel()
    {
        if (!isset($this->label)) {
            if ($this->getParam(self::PARAM_LABEL_ID)) {
                $this->label = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')
                    ->find($this->getParam(self::PARAM_LABEL_ID));

            } else {
                $this->label = false;
            }
        }

        return $this->label;
    }

    /**
     * Get label translation
     *
     * @param string $code Language code
     *
     * @return string
     */
    public function getTranslation($code)
    {
        return strval($this->getLabel()->getTranslation($code)->label);
    }

    /**
     * Get added languages
     *
     * @return array
     */
    public function getAddedLanguages()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Language')->findAddedLanguages();
    }

    /**
     * Check - is requried language or not
     *
     * @param \XLite\Model\Language $language Language_
     *
     * @return boolean
     */
    public function isRequiredLanguage(\XLite\Model\Language $language)
    {
        return $language->code === static::getDefaultLanguage();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'languages/edit.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_LABEL_ID => new \XLite\Model\WidgetParam\TypeInt(
                'Label id', \XLite\Core\Request::getInstance()->{self::PARAM_LABEL_ID}
            ),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getLabel();
    }
}
