<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Controller\Customer;

/**
 * Product
 */
class Product extends \XLite\Controller\Customer\Product implements \XLite\Base\IDecorator
{

    /**
     * Get variant images 
     * 
     * @return void
     */
    protected function doActionGetVariantImages()
    {
        $data = null;

        if ($this->getProduct()->mustHaveVariants()) {
            $ids = array();
            $attributeValues = trim(\XLite\Core\Request::getInstance()->{\XLite\View\Product\Details\Customer\Widget::PARAM_ATTRIBUTE_VALUES}, ',');

            if ($attributeValues) {
                $attributeValues = explode(',', $attributeValues);
                foreach ($attributeValues as $v) {
                    $v = explode('_', $v);
                    $ids[$v[0]] = $v[1];
                }
            }

            $productVariant = $this->getProduct()->getVariant(
                $this->getProduct()->prepareAttributeValues($ids)
            );

            if ($productVariant && $productVariant->getImage()) {
                $data = $this->assembleVariantImageData($productVariant->getImage());
            }
        }

        $this->displayJSON($data);
        $this->setSuppressOutput(true);
    }

    /**
     * Assemble variant image data 
     * 
     * @param \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image $image Image
     *  
     * @return array
     */
    protected function assembleVariantImageData(\XLite\Model\Base\Image $image)
    {
        $result = array(
            'full' => array(
                $image->getWidth(),
                $image->getHeight(),
                $image->getURL(),
                $image->getAlt(),
            ),
        );

        foreach ($this->getImageSizes() as $name => $sizes) {
            $result[$name] = $image->getResizedURL($sizes[0], $sizes[1]);
            $result[$name][3] = $image->getAlt();
        }

        return $result;
    }

    /**
     * Get image sizes 
     * 
     * @return array
     */
    protected function getImageSizes()
    {
        return array(
            'gallery' => array(
                60,
                60,
            ),
            'main'    => array(
                $this->getDefaultMaxImageSize(true),
                $this->getDefaultMaxImageSize(false),
            ),
        );
    }

}
