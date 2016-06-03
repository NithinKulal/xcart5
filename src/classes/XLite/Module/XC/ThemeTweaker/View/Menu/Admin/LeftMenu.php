<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Menu\Admin;

/**
 * Left menu widget
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        if (empty($this->relatedTargets['layout'])) {
            $this->relatedTargets['layout'] = array();
        }

        if (!in_array('custom_css', $this->relatedTargets['layout'])) {
            $this->relatedTargets['layout'][] = 'custom_css';
            $this->relatedTargets['layout'][] = 'custom_js';
            $this->relatedTargets['layout'][] = 'theme_tweaker_templates';

            if (!\XLite\Core\Request::getInstance()->template) {
                $this->relatedTargets['layout'][] = 'theme_tweaker_template';
            }
        }

        parent::__construct();
    }
}
