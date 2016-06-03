<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use XLite\Core\Database;
use XLite\Core\Event\WidgetAfterRenderEvent;
use XLite\Core\Event\WidgetBeforeRenderEvent;
use XLite\Core\Events;
use XLite\Model\AEntity;
use XLite\Module\XC\WebmasterKit\DebugBar\DataCollector\WidgetTimeDataCollector;
use XLite\Module\XC\WebmasterKit\Logic\DebugBar;

class WidgetRenderSubscriber implements EventSubscriberInterface
{
    /** @var WidgetTimeDataCollector */
    protected $widgetTimeDataCollector;

    /** @var EntityManager */
    protected $em;

    public function __construct()
    {
        $this->widgetTimeDataCollector = DebugBar::getInstance()->getWidgetTimeDataCollector();

        $this->em = Database::getEM();
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::WIDGET_BEFORE_RENDER => 'onStartRender',
            Events::WIDGET_AFTER_RENDER  => 'onFinishRender',
        ];
    }

    public function onStartRender(WidgetBeforeRenderEvent $event)
    {
        $label = get_class($event->getWidget());

        $this->widgetTimeDataCollector->startMeasure($label);
    }

    public function onFinishRender(WidgetAfterRenderEvent $event)
    {
        $widgetData = $event->isVisible()
            ? ['visible' => true, 'cached' => $event->isCached()]
            : ['visible' => false, 'cached' => false];

        $paramToString = function ($param) use (&$paramToString) {
            if (is_array($param)) {
                return '[' . implode(', ', array_map($paramToString, $param)) . ']';
            }

            if (is_bool($param)) {
                return $param ? 'true' : 'false';
            }

            if (is_object($param)) {
                if ($param instanceof AEntity) {
                    $meta = $this->em->getClassMetadata(get_class($param));

                    $formattedId = implode(', ', array_map(
                        function ($v, $k) {
                            return $k . '=' . ($v === null ? 'null' : (string)$v);
                        },
                        $meta->getIdentifierValues($param),
                        $meta->getIdentifierFieldNames()
                    ));

                    return '{' . $meta->getName() . ' ' . $formattedId . '}';
                }

                return '{' . get_class($param) . '}';
            }

            return (string)$param;
        };

        $params = array_map(function ($param) use ($paramToString) {
            return $paramToString($param->value);
        }, $event->getWidget()->getWidgetParams());

        if ($event->getTemplate() != null) {
            $params['template'] = $event->getTemplate();
        }

        $this->widgetTimeDataCollector->stopMeasure($params, $widgetData);
    }
}