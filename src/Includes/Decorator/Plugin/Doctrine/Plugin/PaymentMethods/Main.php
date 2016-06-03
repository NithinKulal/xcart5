<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\PaymentMethods;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    
    /**
     * Methods list (local cache)
     * 
     * @var array
     */
    protected $list;

    /**
     * Check - current plugin is bocking or not
     *
     * @return boolean
     */
    public function isBlockingPlugin()
    {
        return parent::isBlockingPlugin() || $this->getChangedPaymentMethods();
    }

    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method');
        foreach ($this->getChangedPaymentMethods() as $row) {
            $row['method']->setModuleEnabled($row['state']);
            if ($row['state']) {
                $row['method']->setFromMarketplace(false);
            }
            $repo->update($row['method']);
        }

        $this->list = null;
    }

    /**
     * Get changed payment methods 
     * 
     * @return array
     */
    protected function getChangedPaymentMethods()
    {
        if (!isset($this->list)) {
            $this->list = array();
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method');
            foreach ($repo->iterateAll() as $method) {
                $method = $method[0];
                if ($method->getModuleName()) {
                    $isReallyModuleEnabled = (bool)$method->getProcessor();
                    if ($method->getModuleEnabled() != $isReallyModuleEnabled) {
                        $this->list[] = array(
                            'method' => $method,
                            'state'  => $isReallyModuleEnabled,
                        );
                    }
                }
            }
        }

        return $this->list;
    }
}
