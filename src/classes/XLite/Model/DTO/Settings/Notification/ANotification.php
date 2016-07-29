<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Settings\Notification;

use XLite\Model\DTO\Base\CommonCell;

abstract class ANotification extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param \XLite\Model\Notification $object
     *
     * @return string
     */
    abstract protected function getBodyPath($object);

    /**
     * @param \XLite\Model\Notification $object
     */
    protected function init($object)
    {
        $default = [
            'templatesDirectory' => $object->getTemplatesDirectory(),
        ];
        $this->default = new CommonCell($default);

        $scheme = [
            'header'    => [
                'status' => true,
                'link'   => $this->getCommonUrl(),
            ],
            'greeting'  => [
                'status' => true,
                'link'   => $this->getCommonUrl(),
            ],
            'body'      => $this->getBodyPath($object),
            'signature' => [
                'status' => true,
                'link'   => $this->getCommonUrl(),
            ],
        ];
        $this->scheme = new CommonCell($scheme);

        $systemSettings = [
            'name'               => $object->getName(),
            'description'        => $object->getDescription(),
        ];
        $this->system_settings = new CommonCell($systemSettings);

    }

    /**
     * @param \XLite\Model\Notification $object
     * @param array|null                $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $object->setName($this->system_settings->name);
        $object->setDescription($this->system_settings->description);
    }

    /**
     * @return string
     */
    protected function getCommonUrl()
    {
        return \XLite\Core\Converter::buildURL('notification_common');
    }
}
