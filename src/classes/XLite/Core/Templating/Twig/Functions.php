<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig;

use Twig_Environment;
use XLite\Core\Layout;
use XLite\Core\Translation;
use XLite\View\AView;

/**
 * Custom twig functions
 *
 * TODO: Move widget instantiation logic from AView to a separate WidgetFactory
 */
class Functions
{
    protected $translator;

    protected $layout;

    public function __construct()
    {
        $this->translator = Translation::getInstance();

        $this->layout = Layout::getInstance();
    }

    public function widget(Twig_Environment $env, $context, array $arguments = array())
    {
        $nextPositionalArgument = 0;

        $class = null;

        if (isset($arguments[$nextPositionalArgument]) && is_string($arguments[$nextPositionalArgument])) {
            $class = $arguments[$nextPositionalArgument];
            unset($arguments[$nextPositionalArgument]);
            $nextPositionalArgument++;
        }

        if (isset($arguments[$nextPositionalArgument])) {
            // Instantiate widget with parameters passed in $arguments[$nextPositionalArgument]

            $env->getGlobals()['this']->getWidget($arguments[$nextPositionalArgument], $class)->display();
        } else {
            // Instantiate widget with named function arguments

            $env->getGlobals()['this']->getWidget($arguments, $class)->display();
        }
    }

    public function widget_list(Twig_Environment $env, $context, array $arguments = array())
    {
        $type = isset($arguments['type']) ? strtolower($arguments['type']) : null;

        unset($arguments['type']);

        $name = $arguments[0];

        unset($arguments[0]);

        if (isset($arguments[1])) {
            // Instantiate widget list with parameters passed in the second positional argument ($arguments[1])

            $params = $arguments[1];
        } else {
            $params = $arguments;
        }

        if ($type == 'inherited') {
            $env->getGlobals()['this']->displayInheritedViewListContent($name, $params);
        } else if ($type == 'nested') {
            $env->getGlobals()['this']->displayNestedViewListContent($name, $params);
        } else {
            $env->getGlobals()['this']->displayViewListContent($name, $params);
        }
    }

    public function t($name, array $arguments = array(), $code = null)
    {
        return $this->translator->translate($name, $arguments, $code);
    }

    public function url(Twig_Environment $env, $context, $target = '', $action = '', array $params = array(), $forceCuFlag = null)
    {
        return $env->getGlobals()['this']->buildURL($target, $action, $params, $forceCuFlag);
    }

    public function asset($path)
    {
        return $this->layout->getResourceWebPath($path, Layout::WEB_PATH_OUTPUT_URL)
            ?: $this->layout->prepareSkinURL($path, Layout::WEB_PATH_OUTPUT_URL);
    }
}