<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Country
 *
 * @Entity
 * @Table  (name="banner_rotation_slide")
 */
class BannerRotationSlide extends \XLite\Model\AEntity
{
    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Enabled status
     *
     * @var   boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Image
     *
     * @var \XLite\Model\Image\BannerRotationImage
     *
     * @OneToOne  (targetEntity="XLite\Model\Image\BannerRotationImage", mappedBy="bannerRotationSlide", cascade={"all"})
     */
    protected $image;

    /**
     * Link
     *
     * @var string
     *
     * @Column         (type="string")
     */
    protected $link = '';

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return BannerRotationSlide
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return BannerRotationSlide
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Get front link
     *
     * @return string
     */
    public function getFrontLink() {
        $link = $this->getLink();

        if ($link && LC_USE_CLEAN_URLS) {
            $cleanUrlLink = \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildCleanUrlByString($link);
            $link = $cleanUrlLink ?: $link;
        }

        return $link;
    }
    
    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return BannerRotationSlide
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set image
     *
     * @param \XLite\Model\Image\BannerRotationImage $image
     * @return BannerRotationSlide
     */
    public function setImage(\XLite\Model\Image\BannerRotationImage $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \XLite\Model\Image\BannerRotationImage 
     */
    public function getImage()
    {
        return $this->image;
    }
}
