<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Core\EventListener;

/**
 * Image resize
 */
class ImageResize extends \XLite\Core\EventListener\ImageResize implements \XLite\Base\IDecorator
{
    const CHUNK_LENGTH = 5;
}