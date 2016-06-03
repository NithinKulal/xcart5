<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Image
 */
class Image extends \XLite\View\AView
{
    /**
     * Widget arguments names
     */
    const PARAM_IMAGE             = 'image';
    const PARAM_ALT               = 'alt';
    const PARAM_MAX_WIDTH         = 'maxWidth';
    const PARAM_MAX_HEIGHT        = 'maxHeight';
    const PARAM_SIZE_ID           = 'sizeId';
    const PARAM_CENTER_IMAGE      = 'centerImage';
    const PARAM_VERTICAL_ALIGN    = 'verticalAlign';
    const PARAM_USE_CACHE         = 'useCache';
    const PARAM_USE_DEFAULT_IMAGE = 'useDefaultImage';
    const PARAM_IMAGE_SIZE_TYPE   = 'imageSizeType';


    /**
     * Vertical align types
     */
    const VERTICAL_ALIGN_TOP    = 'top';
    const VERTICAL_ALIGN_MIDDLE = 'middle';
    const VERTICAL_ALIGN_BOTTOM = 'bottom';

    /**
     * Default image (no image) dimensions
     */
    const DEFAULT_IMAGE_WIDTH  = 300;
    const DEFAULT_IMAGE_HEIGHT = 300;

    /**
     * Allowed properties names
     *
     * @var array
     */
    protected $allowedProperties = array(
        'className'   => 'class',
        'id'          => 'id',
        'onclick'     => 'onclick',
        'style'       => 'style',
        'onmousemove' => 'onmousemove',
        'onmouseup'   => 'onmouseup',
        'onmousedown' => 'onmousedown',
        'onmouseover' => 'onmouseover',
        'onmouseout'  => 'onmouseout',
    );

    /**
     * Additioanl properties
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Resized thumbnail URL
     *
     * @var string
     */
    protected $resizedURL = null;

    /**
     * Use default image 
     * 
     * @var boolean
     */
    protected $useDefaultImage = false;

    /**
     * Set widget parameters
     *
     * @param array $params Widget parameters
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        // Save additional parameters
        foreach ($params as $name => $value) {
            if (isset($this->allowedProperties[$name])) {
                $this->properties[$this->allowedProperties[$name]] = $value;
            }
        }

        if ($this->getParam(self::PARAM_MAX_WIDTH) == 0
            && $this->getParam(self::PARAM_MAX_HEIGHT) == 0
            && $this->getParam(self::PARAM_IMAGE_SIZE_TYPE)
        ) {
            list($width, $height) = \XLite\Logic\ImageResize\Generator::getImageSizes(
                \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT,
                $this->getParam(self::PARAM_IMAGE_SIZE_TYPE)
            );

            if ($width && $height) {
                $this->getWidgetParams(self::PARAM_MAX_WIDTH)->setValue($width);
                $this->getWidgetParams(self::PARAM_MAX_HEIGHT)->setValue($height);
            }
        }
    }

    /**
     * Get image URL
     *
     * @return string
     */
    public function getURL()
    {
        $url = null;

        if ($this->getParam(self::PARAM_IMAGE) && $this->getParam(self::PARAM_IMAGE)->isExists()) {
            // Specified image
            $url = $this->getParam(self::PARAM_USE_CACHE)
                ? $this->resizedURL
                : $this->getParam(self::PARAM_IMAGE)->getFrontURL();
        }

        if (!$url && $this->getParam(self::PARAM_USE_DEFAULT_IMAGE)) {
            // Default image
            $url = \XLite::getInstance()->getOptions(array('images', 'default_image'));

            if (!\XLite\Core\Converter::isURL($url)) {
                $url = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    $url,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL
                );
                $this->useDefaultImage = true;
            }
        }

        return $this->prepareURL($url);
    }

    /**
     * Get image alternative text
     *
     * @return void
     */
    public function getAlt()
    {
        return $this->getParam(self::PARAM_ALT);
    }

    /**
     * Get properties
     *
     * @return void
     */
    public function getProperties()
    {
        $this->properties['data-max-width'] = max(0, $this->getParam(self::PARAM_MAX_WIDTH));
        $this->properties['data-max-height'] = max(0, $this->getParam(self::PARAM_MAX_HEIGHT));
        $this->properties['data-is-default-image'] = $this->useDefaultImage;

        return $this->properties;
    }

    /**
     * Remove the protocol from the url definition
     *
     * @param string $url
     *
     * @return string
     */
    protected function prepareURL($url)
    {
        return str_replace(array('http://', 'https://'), '//', $url);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/image.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_IMAGE             => new \XLite\Model\WidgetParam\TypeObject('Image', null, false, '\XLite\Model\Base\Image'),
            self::PARAM_ALT               => new \XLite\Model\WidgetParam\TypeString('Alt. text', '', false),
            self::PARAM_MAX_WIDTH         => new \XLite\Model\WidgetParam\TypeInt('Max. width', 0),
            self::PARAM_MAX_HEIGHT        => new \XLite\Model\WidgetParam\TypeInt('Max. height', 0),
            self::PARAM_CENTER_IMAGE      => new \XLite\Model\WidgetParam\TypeCheckbox('Center the image after resizing', true),
            self::PARAM_VERTICAL_ALIGN    => new \XLite\Model\WidgetParam\TypeString('Vertical align', self::VERTICAL_ALIGN_MIDDLE),
            self::PARAM_USE_CACHE         => new \XLite\Model\WidgetParam\TypeBool('Use cache', 1),
            self::PARAM_USE_DEFAULT_IMAGE => new \XLite\Model\WidgetParam\TypeBool('Use default image', 1),
            self::PARAM_IMAGE_SIZE_TYPE   => new \XLite\Model\WidgetParam\TypeString('Imeage size type', ''),
        );
    }

    /**
     * checkImage
     *
     * @return boolean
     */
    protected function checkImage()
    {
        return $this->getParam(self::PARAM_IMAGE)
            && $this->getParam(self::PARAM_IMAGE)->isExists();
    }

    /**
     * checkDefaultImage
     *
     * @return boolean
     */
    protected function checkDefaultImage()
    {
        return $this->getParam(self::PARAM_USE_DEFAULT_IMAGE)
            && \XLite::getInstance()->getOptions(array('images', 'default_image'));
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible();

        if ($result) {

            if ($this->checkImage()) {
                $this->processImage();

            } elseif ($this->checkDefaultImage()) {
                $this->processDefaultImage();

            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Return a CSS style centering the image vertically and horizontally
     *
     * @return string
     */
    protected function setImageMargin()
    {
        $vertical = ($this->getParam(self::PARAM_MAX_HEIGHT) - $this->properties['height']) / 2;

        switch ($this->getParam(self::PARAM_VERTICAL_ALIGN)) {
            case self::VERTICAL_ALIGN_TOP:
                $top    = 0;
                $bottom = 0;
                break;

            case self::VERTICAL_ALIGN_BOTTOM:
                $top    = $this->getParam(self::PARAM_MAX_HEIGHT) - $this->properties['height'];
                $bottom = 0;
                break;

            default:
                $top    = max(0, ceil($vertical));
                $bottom = max(0, floor($vertical));
        }

        if (0 < $top || 0 < $bottom) {
            $this->addInlineStyle('margin: 0 auto;margin-bottom:' . $bottom . 'px;' . 'margin-top:' . $top . 'px;');
        }
    }

    /**
     * Add CSS styles to the value of "style" attribute of the image tag
     *
     * @param string $style CSS styles to be added to the end of "style" attribute
     *
     * @return void
     */
    protected function addInlineStyle($style)
    {
        if (!isset($this->properties['style'])) {
            $this->properties['style'] = $style;

        } else {
            $this->properties['style'] .= ' ' . $style;
        }
    }

    /**
     * Preprocess image
     * TODO: replace getResizedThumbnailURL to getResizedURL
     *
     * @return void
     */
    protected function processImage()
    {
        $maxw = max(0, $this->getParam(self::PARAM_MAX_WIDTH));
        $maxh = max(0, $this->getParam(self::PARAM_MAX_HEIGHT));

        $funcName = method_exists($this->getParam(self::PARAM_IMAGE), 'getResizedURL')
            ? 'getResizedURL'
            : 'getResizedThumbnailURL';

        // $funcName - getResizedURL or getResizedThumbnailURL
        list(
            $this->properties['width'],
            $this->properties['height'],
            $this->resizedURL
        ) = $this->getParam(self::PARAM_IMAGE)->$funcName($maxw, $maxh);

        // Center the image vertically and horizontally
        if ($this->getParam(self::PARAM_CENTER_IMAGE)) {
            $this->setImageMargin();
        }
    }

    /**
     * Preprocess default image
     *
     * @return void
     */
    protected function processDefaultImage()
    {
        list($this->properties['width'], $this->properties['height']) = \XLite\Core\ImageOperator::getCroppedDimensions(
            static::DEFAULT_IMAGE_WIDTH,
            static::DEFAULT_IMAGE_HEIGHT,
            max(0, $this->getParam(self::PARAM_MAX_WIDTH)),
            max(0, $this->getParam(self::PARAM_MAX_HEIGHT))
        );

        // Center the image vertically and horizontally
        if ($this->getParam(self::PARAM_CENTER_IMAGE)) {
            $this->setImageMargin();
        }
    }
}
