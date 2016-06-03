<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\Doctrine\DBAL\Logging;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;


class ObservableDebugStack extends DebugStack
{
    protected $startQueryObservers = [];

    /**
     * @var SQLLogger Optional $logger that we will forward startQuery and stopQuery calls to
     */
    private $logger;

    public function __construct(SQLLogger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        parent::startQuery($sql, $params, $types);

        if ($this->logger != null) {
            $this->logger->startQuery($sql, $params, $types);
        }

        foreach ($this->startQueryObservers as $observer) {
            call_user_func($observer, $sql, $params, $types);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        parent::stopQuery();

        if ($this->logger != null) {
            $this->logger->stopQuery();
        }
    }

    /**
     * Add an observer that will be called every time query is started
     *
     * @param callable $observer
     */
    public function addStartQueryObserver(callable $observer)
    {
        $this->startQueryObservers[] = $observer;
    }
}
