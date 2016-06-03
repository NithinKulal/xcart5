<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Model;

/**
 * CleanURL
 */
class CleanURL extends \XLite\Model\CleanURL implements \XLite\Base\IDecorator
{
    /**
     * Relation to a product entity
     *
     * @var \XLite\Module\XC\News\Model\NewsMessage
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\News\Model\NewsMessage", inversedBy="cleanURLs")
     * @JoinColumn (name="news_message_id", referencedColumnName="id")
     */
    protected $newsMessage;

    /**
     * Set newsMessage
     *
     * @param \XLite\Module\XC\News\Model\NewsMessage $newsMessage
     * @return CleanURL
     */
    public function setNewsMessage(\XLite\Module\XC\News\Model\NewsMessage $newsMessage = null)
    {
        $this->newsMessage = $newsMessage;
        return $this;
    }

    /**
     * Get newsMessage
     *
     * @return \XLite\Module\XC\News\Model\NewsMessage 
     */
    public function getNewsMessage()
    {
        return $this->newsMessage;
    }
}
