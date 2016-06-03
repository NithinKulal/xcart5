<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;


use XLite\View\AView;

interface EngineInterface
{
    /**
     * Renders a template and returns a string result
     *
     * @param string $templateName Name of template to render
     * @param AView  $thisObject   Object that will be set as "this" context var
     * @param array  $parameters   Optional context vars
     *
     * @return string
     */
    public function render($templateName, AView $thisObject, array $parameters = array());

    /**
     * Outputs a rendered template
     *
     * @param string $templateName Name of template to render
     * @param AView  $thisObject   Object that will be set as "this" context var
     * @param array  $parameters   Optional context vars
     *
     * @return string
     */
    public function display($templateName, AView $thisObject, array $parameters = array());

    /**
     * Mark string value as safe so it won't be (double)escaped in templates
     *
     * @param $string
     *
     * @return object
     */
    public function getSafeValue($string);
}
