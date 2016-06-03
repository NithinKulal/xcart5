<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Logic\Import\Processor;

/**
 * Subscribers import processor
 */
class NewsletterSubscribers extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Subscribers imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'Email Address'       => array(
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_VERIFICATOR     => array($this, 'verifyEmail'),
                static::COLUMN_PROPERTY        => 'email',
            ),
        );
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages() +
            array(
                'SUBSCRIBER-EMAIL-FMT'      => 'Email is in wrong format',
            );
    }

    /**
     * Verify 'email' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    public function verifyEmail($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsEmail($value)) {
            $this->addError('SUBSCRIBER-EMAIL-FMT', array('column' => $column, 'value' => $value));
        }
    }

    // }}}
}
