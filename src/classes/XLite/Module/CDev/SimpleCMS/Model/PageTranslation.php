<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model;

/**
 * Page
 *
 * @Entity
 * @Table (name="page_translations",
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class PageTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $name;

    /**
     * Teaser
     * todo: rename to $metaDesc
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $teaser;

    /**
     * Content
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $body;

    /**
     * Meta keywords
     * todo: rename to $metaTags, set column type: (type="string", length=255)
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $metaKeywords = '';

    /**
     * Value of the title HTML-tag for category page
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $metaTitle = '';

    /**
     * Set name
     *
     * @param string $name
     * @return PageTranslation
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

    /**
     * Set teaser
     *
     * @param text $teaser
     * @return PageTranslation
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
        return $this;
    }

    /**
     * Get teaser
     *
     * @return text 
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Set body
     *
     * @param text $body
     * @return PageTranslation
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return text 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set metaKeywords
     *
     * @param text $metaKeywords
     * @return PageTranslation
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return text 
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return PageTranslation
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string 
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
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
     * @return PageTranslation
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
