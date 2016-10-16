<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Module\XC\RESTAPI\Core\Schema\Complex;

/**
 * Complex schema
 *
 * @Decorator\Depend ("XC\RESTAPI")
 */
class Profile extends \XLite\Module\XC\RESTAPI\Core\Schema\Complex\Profile implements \XLite\Base\IDecorator
{
    /**
     * Convert model (order)
     *
     * @param \XLite\Model\AEntity $model Entity
     * @param boolean $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $data = parent::convertModel($model, $withAssociations);

        $newData = [
            'socialLoginProvider'   => $model->getSocialLoginProvider() ?: '',
            'socialLoginId'         => $model->getSocialLoginId() ?: '',
            'pictureUrl'            => $model->getPictureUrl() ?: '',
        ];

        return array_merge(
            $data,
            $newData
        );
    }
}
