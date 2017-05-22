<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;


class SimplePromoBlock extends \XLite\View\AView
{
    const PARAM_PROMO_ID    = 'promoId';
    const PARAM_CLASSES     = 'promoClasses';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PROMO_ID  => new \XLite\Model\WidgetParam\TypeString('Promo id', '', ''),
            static::PARAM_CLASSES   => new \XLite\Model\WidgetParam\TypeCollection('Promo classes', '', []),
        ];
    }

    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [ 'promotions/simple_block/styles.less' ]
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'promotions/simple_block/body.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getPromoContent()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * @return string
     */
    public function getPromoId()
    {
        return $this->getParam(static::PARAM_PROMO_ID);
    }

    /**
     * @return string
     */
    public function getAdditionalClasses()
    {
        $classes = $this->getParam(static::PARAM_CLASSES);

        return is_array($classes)
            ? implode(' ', $classes)
            : '';
    }

    /**
     * @return string
     */
    public function getPromoContent()
    {
        return \XLite\Core\Promo::getInstance()->getPromoContent(
            $this->getPromoId()
        );
    }

}
