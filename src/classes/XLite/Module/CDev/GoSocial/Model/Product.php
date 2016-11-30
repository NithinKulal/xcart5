<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model;

/**
 * Product 
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $ogMeta = '';

    /**
     * User Open graph meta tags generator flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useCustomOG = false;

    /**
     * Get Open Graph meta tags
     *
     * @param boolean $preprocessed Preprocessed OPTIONAL
     *
     * @return string
     */
    public function getOpenGraphMetaTags($preprocessed = true)
    {
        $tags = $this->getUseCustomOG()
            ? $this->getOgMeta()
            : $this->generateOpenGraphMetaTags();

        return $preprocessed ? $this->preprocessOpenGraphMetaTags($tags) : $tags;
    }

    /**
     * Define Open Graph meta tags
     *
     * @return array
     */
    protected function defineOpenGraphMetaTags()
    {
        $list = array(
            'og:title'       => $this->getOpenGraphTitle(),
            'og:type'        => $this->getOpenGraphType(),
            'og:url'         => $this->getOpenGraphURL(),
            'og:site_name'   => $this->getOpenGraphSiteName(),
            'og:description' => $this->getOpenGraphDescription(),
            'og:locale'      => $this->getOpenGraphLocale(),
        );

        if ($this->getImage()) {
            $list['og:image'] = $this->getOpenGraphImage();
            $list['og:image:width'] = $this->getImage()->getWidth();
            $list['og:image:height'] = $this->getImage()->getHeight();
        }

        $appId = $this->getOpenGraphAppId();
        $admins = $this->getOpenGraphAdmins();
        if ($appId) {
            $list['fb:app_id'] = $appId;

        } elseif ($admins) {
            $list['fb:admins'] = $admins;
        }

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_use) {
            $list = array_merge(
                $list,
                $this->defineTwitterOpenGraphMetaTags()
            );
        }

        return $list;
    }

    /**
     * Define Open Graph meta tags for twitter
     *
     * @return array
     */
    protected function defineTwitterOpenGraphMetaTags()
    {
        $list = array(
            'twitter:card'              => 'summary',
            'twitter:title'             => $this->getOpenGraphTitle(),
            'twitter:site'              => $this->getOpenGraphSiteName(),
            'twitter:description'       => $this->getOpenGraphDescription() ?: $this->getOpenGraphTitle(),
        );

        if ($this->getImage()) {
            $list['twitter:image'] = $this->getOpenGraphImage();
        }

        return $list;
    }
    /**
     * Returns open graph title
     *
     * @return string
     */
    protected function getOpenGraphTitle()
    {
        return $this->getName();
    }

    /**
     * Returns open graph type
     *
     * @return string
     */
    protected function getOpenGraphType()
    {
        return 'article';
    }

    /**
     * Returns open graph url
     *
     * @return string
     */
    protected function getOpenGraphURL()
    {
        return '[PAGE_URL]';
    }

    /**
     * Returns open graph site name
     *
     * @return string
     */
    protected function getOpenGraphSiteName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Returns open graph description
     *
     * @return string
     */
    protected function getOpenGraphDescription()
    {
        $result = strip_tags($this->getBriefDescription());

        if (!$result) {
            $result = strip_tags($this->getDescription());
        }

        return $result;
    }

    /**
     * Returns open graph locale
     *
     * @return string
     */
    protected function getOpenGraphLocale()
    {
        return 'en_US';
    }

    /**
     * Returns open graph image
     *
     * @return string
     */
    protected function getOpenGraphImage()
    {
        return '[IMAGE_URL]';
    }

    /**
     * Returns open graph app id
     *
     * @return string
     */
    protected function getOpenGraphAppId()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id;
    }

    /**
     * Returns open graph admins
     *
     * @return string
     */
    protected function getOpenGraphAdmins()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_admins;
    }

    /**
     * Get generated Open Graph meta tags
     *
     * @return string
     */
    protected function generateOpenGraphMetaTags()
    {
        $list = $this->defineOpenGraphMetaTags();

        $html = array();
        foreach ($list as $k => $v) {
            $html[] = '<meta property="' . $k . '" content="' . htmlentities($v, ENT_COMPAT, 'UTF-8') . '" />';
        }

        return implode("\n", $html);
    }

    /**
     * Preprocess Open Graph meta tags
     *
     * @param string $tags Tags content
     *
     * @return string
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        return str_replace(
            array(
                '[PAGE_URL]',
                '[IMAGE_URL]',
            ),
            array(
                $this->getFrontURL(),
                $this->getImage() ? $this->getImage()->getFrontURL() : '',
            ),
            $tags
        );
    }

    /**
     * Set ogMeta
     *
     * @param text $ogMeta
     * @return Product
     */
    public function setOgMeta($ogMeta)
    {
        $this->ogMeta = $ogMeta;
        return $this;
    }

    /**
     * Get ogMeta
     *
     * @return text 
     */
    public function getOgMeta()
    {
        return $this->ogMeta;
    }

    /**
     * Set useCustomOG
     *
     * @param boolean $useCustomOG
     * @return Product
     */
    public function setUseCustomOG($useCustomOG)
    {
        $this->useCustomOG = $useCustomOG;
        return $this;
    }

    /**
     * Get useCustomOG
     *
     * @return boolean 
     */
    public function getUseCustomOG()
    {
        return $this->useCustomOG;
    }
}
