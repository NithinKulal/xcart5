<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Config multilingual data
 *
 * @Entity
 * @Table  (name="config_translations",
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class ConfigTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Human-readable option name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $option_name;

    /**
     * Option comment
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $option_comment = '';

    /**
     * Set option_name
     *
     * @param string $optionName
     * @return ConfigTranslation
     */
    public function setOptionName($optionName)
    {
        $this->option_name = $optionName;
        return $this;
    }

    /**
     * Get option_name
     *
     * @return string 
     */
    public function getOptionName()
    {
        return $this->option_name;
    }

    /**
     * Set option_comment
     *
     * @param text $optionComment
     * @return ConfigTranslation
     */
    public function setOptionComment($optionComment)
    {
        $this->option_comment = $optionComment;
        return $this;
    }

    /**
     * Get option_comment
     *
     * @return text 
     */
    public function getOptionComment()
    {
        return $this->option_comment;
    }

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
     * @return ConfigTranslation
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
