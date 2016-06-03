<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;

use Includes\Decorator\Utils\CacheManager;
use Twig_Environment;
use Twig_Extension_Debug;
use XLite\Core\Templating\Twig\Extension\XCart;
use XLite\Core\Templating\Twig\NodeVisitor\CExtDisablingNodeVisitor;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;

/**
 * Twig templating engine
 */
abstract class AbstractTwigEngine
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    public function __construct(\Twig_LoaderInterface $loader)
    {
        $this->twig = new Twig_Environment($loader, array(
            'cache'               => CacheManager::getCompileDir() . 'skins/',
            'debug'               => LC_DEVELOPER_MODE,
            'base_template_class' => '\\XLite\\Core\\Templating\\Twig\\Template',
        ));

        $this->twig->addNodeVisitor(new CExtDisablingNodeVisitor());

        $this->twig->addExtension(new Twig_Extension_Debug());

        $this->twig->addExtension(new XCart());

        /** @todo: marge theme to one file */
        $formEngine = new TwigRendererEngine(array('twig_form/bootstrap_3_horizontal_layout.html.twig'));
        $formEngine->setEnvironment($this->twig);

        $this->twig->addExtension(
            new FormExtension(new TwigRenderer($formEngine))
        );
    }
}
