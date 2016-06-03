<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Recover password dialog
 *
 * @ListChild (list="center")
 */
class RecoverPassword extends \XLite\View\SectionDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'recover_password';

        return $list;
    }


    /**
     * Define sections list
     *
     * @return array
     */
    protected function defineSections()
    {
        return array(
            '' => array(
                'body' => 'recover_password/recover_password.twig',
            ),
            'recoverMessage' => array(
                'head' => 'Recover password',
                'body' => 'recover_message.twig',
            ),
        );
    }
}
