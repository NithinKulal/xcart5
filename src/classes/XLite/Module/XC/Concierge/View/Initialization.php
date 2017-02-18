<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Module\XC\Concierge\Core\AMessage;
use XLite\Module\XC\Concierge\Core\Message\Page;

/**
 * Initialization
 *
 * @ListChild (list="head", zone="admin")
 */
class Initialization extends \XLite\View\AView
{
    use ExecuteCachedTrait;

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/Concierge/head.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && $this->getMediator()->getWriteKey();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Concierge/head.twig';
    }

    /**
     * Get mediator
     *
     * @return \XLite\Module\XC\Concierge\Core\Mediator
     */
    protected function getMediator()
    {
        return \XLite\Module\XC\Concierge\Core\Mediator::getInstance();
    }

    /**
     * Get messages
     *
     * @return array
     */
    protected function getMessages()
    {
        return $this->executeCachedRuntime(function () {
            return $this->prepareMessages($this->defineMessages());
        });
    }

    /**
     * @return array
     */
    protected function defineMessages()
    {
        $controller = \XLite::getController();

        $t = array_merge(
            $this->getMediator()->getIdentifyMessage(),
            [
                new Page($controller->getConciergeCategory(), $controller->getConciergeTitle()),
            ],
            $this->getMediator()->getMessages()
        );

        return $t;
    }

    /**
     * @param AMessage[] $messages
     *
     * @return array
     */
    protected function prepareMessages($messages)
    {
        return array_map(function ($message) {
            return $message instanceof AMessage ? $message->toArray() : $message;
        }, $messages);
    }

    /**
     * Check - debug mode or not
     *
     * @return boolean
     */
    protected function isDebug()
    {
        return false;
    }

    /**
     * Get settings
     *
     * @return array
     */
    protected function getSettings()
    {
        return [
            'writeKey' => $this->getMediator()->getWriteKey(),
            'messages' => $this->getMessages(),
            'ready'    => true,
            'context'  => $this->getMediator()->getOptions(),
        ];
    }
}
