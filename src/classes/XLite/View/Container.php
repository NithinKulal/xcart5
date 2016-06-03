<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Abstract container widget
 *
 * :TODO:  waiting for the multiple inheritance
 * :FIXME: must extend the AView class
 */
abstract class Container extends \XLite\View\RequestHandler\ARequestHandler
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    abstract protected function getDir();

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * isWrapper
     *
     * @return boolean
     */
    protected function isWrapper()
    {
        return $this->getParam(self::PARAM_TEMPLATE) == $this->getDefaultTemplate();
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'body.twig';
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->useBodyTemplate() ? $this->getBody() : parent::getTemplate();
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return $this->getDir() . LC_DS . $this->getBodyTemplate();
    }

    /**
     * Determines if need to display only a widget body
     *
     * @return boolean
     */
    protected function useBodyTemplate()
    {
        return \XLite\Core\CMSConnector::isCMSStarted() && $this->isWrapper();
    }
}
