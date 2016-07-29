<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Image settings model
 *
 * @Entity
 * @Table  (name="images_settings",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="code_model_module", columns={"code", "model", "moduleName"})
 *      })
 */
class ImageSettings extends \XLite\Model\AEntity
{
    /**
     * Unique Id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Image size code
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $code;

    /**
     * Model (class name of image model)
     *
     * @var string
     *
     * @Column (type="string", length=200)
     */
    protected $model;

    /**
     * Skin module name - owner of image sizes
     *
     * @var string
     *
     * @Column (type="string", length=200)
     */
    protected $moduleName;

    /**
     * Image max width
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $width;

    /**
     * Image max height
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $height;

    /**
     * Get image setting name
     *
     * @return string
     */
    public function getName()
    {
        return static::t('imgsize-' . $this->getImageType() . '-' .$this->getCode());
    }

    /**
     * Get image type by model class
     *
     * @return string
     */
    protected function getImageType()
    {
        $imageTypes = $this->getImageTypes();

        return !empty($imageTypes[$this->getModel()]) ? $imageTypes[$this->getModel()] : $this->getModel();
    }

    /**
     * Get list of available image size types
     *
     * @return array
     */
    protected function getImageTypes()
    {
        return array(
            \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT => 'product',
            \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY => 'category',
        );
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
     * Set code
     *
     * @param string $code
     * @return ImageSettings
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

    /**
     * Set model
     *
     * @param string $model
     * @return ImageSettings
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get model
     *
     * @return string 
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return ImageSettings
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return ImageSettings
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set module name
     *
     * @param string $moduleName
     * @return ImageSettings
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
