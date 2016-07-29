<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\Button;

/**
 * ComingSoon button
 */
class ComingSoon extends \XLite\View\Button\Link
{
    const PARAM_TOOLTIP = 'tooltip';

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/BulkEditing/button/coming_soon.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_TOOLTIP => new \XLite\Model\WidgetParam\TypeString('Tooltip', static::t('This feature is not yet available. You will be redirected to the feature request page where you can vote to have this feature implemented in one of the upcoming versions.')),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/BulkEditing/button/coming_soon.twig';
    }
}
