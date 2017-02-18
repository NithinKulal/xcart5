<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Core;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Layout manager
 */
class LessParser extends \XLite\Core\LessParser implements \XLite\Base\IDecorator
{
    use ExecuteCachedTrait;

    /**
     * @param array $data Resource data
     *
     * @return array
     */
    protected function getModifiedLESSVars($data)
    {
        return array_merge(
            parent::getModifiedLESSVars($data),
            $this->getAdditionalLessData()
        );
    }

    /**
     * Calc LESSResourceHash
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return array
     */
    protected function calcLESSResourceHash($lessFiles)
    {
        $result = parent::calcLESSResourceHash($lessFiles);
        $additionalData = $this->getAdditionalLessData();

        foreach ($result as $k => $v) {
            $result[$k] = md5(serialize([$v, $additionalData]));
        }

        return $result;
    }

    /**
     * Create a unique name for the less files collection
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return string
     */
    protected function getUniqueName($lessFiles)
    {
        $result = parent::getUniqueName($lessFiles);
        $additionalData = $this->getAdditionalLessData();

        return hash('md4', $result . serialize($additionalData));
    }

    /**
     * @return array
     */
    protected function getAdditionalLessData()
    {
        return $this->executeCachedRuntime(function () {
            $categoryImageSize = \XLite\Logic\ImageResize\Generator::getImageSizes(
                \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                'Default'
            );

            list($width, $height) = $categoryImageSize;

            return [
                'layout-category-image-width'  => $width . 'px',
                'layout-category-image-height' => $height . 'px',
            ];
        });
    }
}
