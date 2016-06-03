<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Marketplace
 */
class Marketplace extends \XLite\Controller\Admin\AAdmin
{
    protected $data = array();

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('update'));
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $content = json_encode($this->data);

        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Length: ' . strlen($content));
        header('ETag: ' . md5($content));

        print ($content);
    }

    /**
     * 'Update' action
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        // Update info about payment methods
        \XLite\Core\Marketplace::getInstance()->updatePaymentMethods();

        // Update info about shipping methods
        \XLite\Core\Marketplace::getInstance()->updateShippingMethods();

        // Run get_dataset query for expired actions
        $result = \XLite\Core\Marketplace::getInstance()->getDataset();

        if (empty($result) ) {
            $result = array();
        }

        $data = array(
            'actions' => array_keys($result),
        );

        if (!empty($result['check_for_updates'])) {
            $data['check_for_updates_data'] = (0 < array_sum($result['check_for_updates']));
        }

        if (isset($result['get_addons'])) {
            $data['get_addons_data'] = !empty($result['get_addons']) && is_array($result['get_addons']);
        }

        $this->data = $data;
    }
}
