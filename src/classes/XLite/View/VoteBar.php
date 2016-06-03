<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Vote bar widget
 */
class VoteBar extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_RATE        = 'rate';
    const PARAM_MAX         = 'max';
    const PARAM_IS_EDITABLE = 'is_editable';
    const PARAM_FIELD_NAME  = 'field_name';

    const STARS_KOEFFICIENT = 0;

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'vote_bar/vote_bar.css';

        return $list;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'vote_bar/vote_bar.twig';
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
            self::PARAM_RATE        => new \XLite\Model\WidgetParam\TypeFloat('', 0),
            self::PARAM_MAX         => new \XLite\Model\WidgetParam\TypeInt('', 5),
            self::PARAM_IS_EDITABLE => new \XLite\Model\WidgetParam\TypeBool('', false),
            self::PARAM_FIELD_NAME  => new \XLite\Model\WidgetParam\TypeString('', 'rating'),
        );
    }

    /**
     * Get field name
     *
     * @return string
     */
    protected function getFieldName()
    {
        return $this->getParam(self::PARAM_FIELD_NAME);
    }

    /**
     * Get rating
     *
     * @return float
     */
    protected function getRating()
    {
        return $this->getParam(self::PARAM_RATE);
    }

    /**
     * Get percent
     *
     * @return integer
     */
    protected function getPercent()
    {
        // Percent plus correction (1 pixel per marked star)
        return intval(
            $this->getParam(self::PARAM_RATE) * 100 / $this->getParam(self::PARAM_MAX)
            + $this->getParam(self::PARAM_RATE) * static::STARS_KOEFFICIENT
        );
    }

    /**
     * Get number or stars
     *
     * @return integer
     */
    protected function getStarsCount()
    {
        return range(1, $this->getParam(self::PARAM_MAX), 1);
    }

    /**
     * Get number or stars
     *
     * @return integer
     */
    protected function isEditable()
    {
        return $this->getParam(self::PARAM_IS_EDITABLE);
    }
}
