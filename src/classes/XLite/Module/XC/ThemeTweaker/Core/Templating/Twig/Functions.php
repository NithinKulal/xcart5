<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Templating\Twig;

use Twig_Environment;
use XLite\Core\Layout;

class Functions
{
    protected $layout;

    public function __construct()
    {
        $this->layout = Layout::getInstance();
    }

    public function xcart_include(Twig_Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
    {
        /** @var \XLite\View\AView $view */
        $view = $env->getGlobals()['this'];
        $result = '';

        $fullPath = $this->layout->getResourceFullPath($template);
        list($templateWrapperText, $templateWrapperStart) = $view->startMarker($fullPath);
        if ($templateWrapperText) {
            $result .= $templateWrapperStart;
        }

        $result .= twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);

        if ($templateWrapperText) {
            $result .= $view->endMarker($fullPath, $templateWrapperText);
        }

        return $result;
    }
}
