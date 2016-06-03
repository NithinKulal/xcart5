<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Admin;

/**
 * Main page controller
 */
class Main extends \XLite\Controller\Admin\Main implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array(
                'hide_welcome_block_paypal',
                'hide_welcome_block_paypal_forever',
            )
        );
    }

    /**
     * Hide welcome block
     *
     * @return void
     */
    protected function doActionHideWelcomeBlockPaypal()
    {
        \XLite\Core\Session::getInstance()->hide_welcome_block_paypal = 1;

        print ('OK');

        $this->setSuppressOutput(true);
    }

    /**
     * Hide welcome block (forever)
     *
     * @return void
     */
    protected function doActionHideWelcomeBlockPaypalForever()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Paypal',
                'name'     => 'show_admin_welcome',
                'value'    => 'N',
            )
        );

        print ('OK');

        $this->setSuppressOutput(true);
    }
}
