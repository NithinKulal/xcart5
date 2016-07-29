<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
 namespace XLite\Module\CDev\XPaymentsConnector\View\Tabs;

/**
 * Profile dialog
 *
 * 
 */
class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return void
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'saved_cards';
        $list[] = 'add_new_card';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {

        $tabs = parent::defineTabs();

        $cnd = new \XLite\Core\CommonCell();
        $cnd->class = 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\SavedCard';

        $saveCardsMethods = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd);

        if ($saveCardsMethods) {
            $found = false;
            foreach ($saveCardsMethods as $pm) {
                if ($pm->isEnabled()) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $tabs['saved_cards'] = array(
                    'weight'   => 1000,
                    'title'    => static::t('Saved credit cards'),
                    'template' => 'modules/CDev/XPaymentsConnector/account/saved_cards.twig',
                );
            }
        }

        return $tabs;
    }
}

