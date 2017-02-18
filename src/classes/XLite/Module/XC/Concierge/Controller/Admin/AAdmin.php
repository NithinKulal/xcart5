<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Controller\Admin;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Track;

/**
 * Abstract customer controller
 */
abstract class AAdmin extends \XLite\Controller\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * return string
     */
    public function getConciergeCategory()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getConciergeTitle()
    {
        return $this->getTitle();
    }

    protected function doActionChangeLanguage()
    {
        $session         = \XLite\Core\Session::getInstance();
        $oldLanguageCode = $session->getLanguage()->getCode();

        parent::doActionChangeLanguage();

        $newLanguageCode = $session->getLanguage()->getCode();

        if ($oldLanguageCode !== $newLanguageCode) {
            Mediator::getInstance()->addMessage(
                new Track(
                    'Change Language',
                    [
                        'From' => $oldLanguageCode,
                        'To'   => $newLanguageCode,
                    ]
                )
            );
        }
    }
}
