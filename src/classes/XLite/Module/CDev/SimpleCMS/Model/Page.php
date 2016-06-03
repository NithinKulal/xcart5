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
 * @Table  (name="pages",
 *      indexes={
 *          @Index (name="enabled", columns={"enabled"}),
 *      }
 * )
 */
class Page extends \XLite\Model\Base\Catalog
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Is menu enabled or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * One-to-one relation with page_images table
     *
     * @var \XLite\Module\CDev\SimpleCMS\Model\Image\Page\Image
     *
     * @OneToOne  (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Image\Page\Image", mappedBy="page", cascade={"all"})
     */
    protected $image;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="page", cascade={"all"})
     * @OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * Meta description type
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $metaDescType = 'A';

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURL()
    {
        $result = null;

        if ($this->getId()) {
            $result = \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL('page', '', array('id' => $this->getId()), 'cart.php', true)
                )
            );
        }

        return $result;
    }

    /**
     * Returns meta description
     * todo: rename to getMetaDesc()
     *
     * @return string
     */
    public function getTeaser()
    {
        return 'A' === $this->getMetaDescType() || !$this->getSoftTranslation()->getTeaser()
            ? strip_tags($this->getBody())
            : $this->getSoftTranslation()->getTeaser();
    }

    /**
     * Returns meta description type
     *
     * @return string
     */
    public function getMetaDescType()
    {
        $result = $this->metaDescType;

        if (!$result) {
            $metaDescPresent = array_reduce($this->getTranslations()->toArray(), function ($carry, $item) {
                return $carry ?: (bool) $item->getMetaDesc();
            }, false);

            $result = $metaDescPresent ? 'C' : 'A';
        }

        return $result;
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
     * @return Page
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
     * Set metaDescType
     *
     * @param string $metaDescType
     * @return Page
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
        return $this;
    }

    /**
     * Set image
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Image\Page\Image $image
     * @return Page
     */
    public function setImage(\XLite\Module\CDev\SimpleCMS\Model\Image\Page\Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Image\Page\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return Page
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;
        return $this;
    }

    /**
     * Get cleanURLs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
    }
}
