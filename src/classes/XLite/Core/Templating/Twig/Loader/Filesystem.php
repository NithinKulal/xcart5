<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Loader;

use XLite\Core\Templating\TemplateFinderInterface;

/**
 * Custom Filesystem loader that exposes findTemplate publicly as getTemplatePath
 * (for ex. to be used in diagnostic purposes when loading templates)
 */
class Filesystem extends \Twig_Loader_Filesystem implements TemplateFinderInterface
{
    public function getTemplatePath($name)
    {
        return $this->findTemplate($name, false);
    }
}