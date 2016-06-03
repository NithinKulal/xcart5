<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;

use Twig_LoaderInterface;
use Twig_Markup;
use XLite\Core\Templating\Twig\Loader\Filesystem;
use XLite\View\AView;

/**
 * Twig templating engine
 */
class TwigEngine extends AbstractTwigEngine implements EngineInterface, TemplateFinderInterface
{
    /**
     * @var Twig_LoaderInterface
     */
    protected $loader;

    protected static $instance;

    public function __construct($paths)
    {
        $this->loader = new Filesystem($paths);

        parent::__construct($this->loader);
    }

    /**
     * Renders a template and returns a string result
     *
     * @param string $templateName Name of template to render
     * @param AView  $thisObject   Object that will be set as "this" context var
     * @param array  $parameters   Optional context vars
     *
     * @return string
     */
    public function render($templateName, AView $thisObject, array $parameters = array())
    {
        $template = $this->twig->loadTemplate($templateName);

        $globals = $this->twig->getGlobals();
        $oldThis = array_key_exists('this', $globals) ? $globals['this'] : null;

        $this->twig->addGlobal('this', $thisObject);

        $content = $template->render($parameters);

        $this->twig->addGlobal('this', $oldThis);

        return $content;
    }

    /**
     * Outputs a rendered template
     *
     * @param string $templateName Name of template to render
     * @param AView  $thisObject   Object that will be set as "this" context var
     * @param array  $parameters   Optional context vars
     *
     * @return string
     */
    public function display($templateName, AView $thisObject, array $parameters = array())
    {
        $template = $this->twig->loadTemplate($templateName);

        $globals = $this->twig->getGlobals();
        $oldThis = array_key_exists('this', $globals) ? $globals['this'] : null;

        $this->twig->addGlobal('this', $thisObject);

        $template->display($parameters);

        $this->twig->addGlobal('this', $oldThis);
    }

    /**
     * Mark string value as safe so it won't be (double)escaped in templates
     *
     * @param $string
     *
     * @return object
     */
    public function getSafeValue($string)
    {
        return new Twig_Markup($string, null);
    }

    /**
     * Warms up a template cache by loading template with specified template name
     *
     * @param string $templateName
     */
    public function compile($templateName)
    {
        $this->twig->loadTemplate($templateName);
    }

    /**
     * Tokenize
     *
     * @param string $source
     * @param string $identifier
     */
    public function tokenize($source, $identifier)
    {
        return $this->twig->tokenize($source, $identifier);
    }

    /**
     * Parse
     *
     * @param string $tokensStream
     */
    public function parse($tokensStream)
    {
        return $this->twig->parse($tokensStream);
    }

    /**
     * Find template file by template name
     *
     * (TemplateFinderInterface)
     *
     * @param string $templateName
     *
     * @return bool|string
     */
    public function getTemplatePath($templateName)
    {
        return $this->loader->getTemplatePath($templateName);
    }
}
