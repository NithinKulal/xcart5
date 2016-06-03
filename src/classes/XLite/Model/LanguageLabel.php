<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Language label
 *
 * @Entity
 * @Table (name="language_labels",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="name", columns={"name"})
 *      }
 * )
 */
class LanguageLabel extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $label_id;

    /**
     * Label name
     *
     * @var string
     *
     * @Column (type="string", length=255, options={"collation":"utf8_bin"})
     */
    protected $name;

    /**
     * Get label translation 
     * 
     * @param string $code Language code OPTIONAL
     *  
     * @return \XLite\Model\LanguageLabelTranslation
     */
    public function getLabelTranslation($code = null)
    {
        $result = null;

        $query = \XLite\Core\Translation::getLanguageQuery($code);
        foreach ($query as $code) {
            $result = $this->getTranslation($code, true);
            if (isset($result) || 'en' == $code) {
                break;
            }
        }

        return $result;
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
     * Set name
     *
     * @param string $name
     * @return LanguageLabel
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
