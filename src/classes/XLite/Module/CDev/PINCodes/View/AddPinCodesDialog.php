<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View;

/**
 * Add pin codes dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AddPinCodesDialog extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'add_pin_codes';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/PINCodes/add_pin_codes_dialog/style.css';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Add PIN codes';
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'modules/CDev/PINCodes/add_pin_codes_dialog/body.twig';
    }
}
