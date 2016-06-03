<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Doctrine UnitOfWork data collector
 */
class DoctrineUOWDataCollector extends DataCollector implements Renderable
{
    protected $uow;

    public function __construct(\Doctrine\ORM\UnitOfWork $uow)
    {
        $this->uow = $uow;
    }

    protected function getUnitOfWorkObjects()
    {
        return $this->uow->getIdentityMap();
    }

    protected function getUnitOfWorkSize()
    {
        return $this->uow->size();
    }

    public function collect()
    {
        $mapped = array_map(
            function($object) {
                return count($object);
            },
            $this->getUnitOfWorkObjects()
        );

        arsort($mapped);

        return [
            'size'      =>  $this->getUnitOfWorkSize(),
            'objects'   =>  $mapped
        ];
    }

    public function getName()
    {
        return 'doctrine_uow';
    }

    public function getWidgets()
    {
        return [
            "doctrine UoW" => [
                "icon"    => "tasks",
                "widget"  => "PhpDebugBar.XCartWidgets.DoctrineUOWWidget",
                "map"     => "doctrine_uow.objects",
                "default" => "{}",
            ],
            "doctrine UoW:badge" => [
                "map"     => "doctrine_uow.size",
                "default" => 0,
            ],
        ];
    }
}
