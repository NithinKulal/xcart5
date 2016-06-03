<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Top message
 *
 * @ListChild (list="layout.main", weight="100")
 */
class TopMessage extends \XLite\View\AView
{
    /**
     * Messages 
     * 
     * @var array
     */
    protected $messages;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';
        $list[] = array(
            'file'  => $this->getDir() . '/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'top_message';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if top messages are present
     *
     * @return boolean
     */
    protected function hasTopMessages()
    {
        return (bool) $this->getTopMessages();
    }

    /**
     * Returns top messages
     *
     * @return array
     */
    protected function getTopMessages()
    {
        if (!isset($this->messages)) {
            $this->messages = \XLite\Core\TopMessage::getInstance()->unloadPreviousMessages();
        }

        return $this->messages;
    }

    /**
     * Get message text
     *
     * @param array $data Message
     *
     * @return string
     */
    protected function getText(array $data)
    {
        return $data[\XLite\Core\TopMessage::FIELD_TEXT];
    }

    /**
     * Get message type
     *
     * @param array $data Message
     *
     * @return string
     */
    protected function getType(array $data)
    {
        return $data[\XLite\Core\TopMessage::FIELD_TYPE];
    }

    /**
     * Get message prefix
     *
     * @param array $data Message
     *
     * @return string|void
     */
    protected function getPrefix(array $data)
    {
        switch ($data[\XLite\Core\TopMessage::FIELD_TYPE]) {

            case \XLite\Core\TopMessage::ERROR:
                $prefix = 'Error';
                break;

            case \XLite\Core\TopMessage::WARNING:
                $prefix = 'Warning';
                break;

            default:
                // ...
        }

        return isset($prefix) ? (static::t($prefix) . ':') : '';
    }

    /**
     * Check id box is visible or not
     *
     * :TODO: check if it's really needed, or it's possible to use "isVisible()" instead
     *
     * @return boolean
     */
    protected function isHidden()
    {
        return !$this->hasTopMessages();
    }

    /**
     * Get a specific path
     *
     * @return string
     */
    protected function getPath()
    {
        return \XLite\Core\TopMessage::getInstance()->getPath();
    }
}
