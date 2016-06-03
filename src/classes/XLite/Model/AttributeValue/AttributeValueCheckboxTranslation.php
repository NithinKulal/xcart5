<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Attribute value (checkbox) multilingual data
 *
 * @Entity
 *
 * @Table  (name="attribute_values_checkbox_translations",
 *         indexes={
 *              @Index (name="ci", columns={"code","id"}),
 *              @Index (name="id", columns={"id"})
 *         }
 * )
 */
class AttributeValueCheckboxTranslation extends \XLite\Model\Base\Translation
{

    /**
     * Get label_id
     *
     * @return integer 
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return AttributeValueCheckboxTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
