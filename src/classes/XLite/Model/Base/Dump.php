<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * Dump entity - without DB storage
 */
abstract class Dump extends \XLite\Model\AEntity
{

    /**
     * Unique ID
     *
     * @var integer
     */
    protected $id;

    /**
     * Get entity repository
     *
     * @return \XLite\Model\Doctrine\Repo\AbstractRepo
     */
    public function getRepository()
    {
        return null;
    }

    /**
     * Update entity
     *
     * @return boolean
     */
    public function update()
    {
        return true;
    }

    /**
     * Delete entity
     *
     * @return boolean
     */
    public function delete()
    {
        return true;
    }

    /**
     * Get entity unique identifier name
     *
     * @return string
     */
    public function getUniqueIdentifierName()
    {
        return 'id';
    }

   /**
     * Process files
     *
     * @param mixed $file       File
     * @param array $data       Data to save
     * @param array $properties Properties
     *
     * @return void
     */
    protected function processFile($file, $data, $properties)
    {
        return true;
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        return clone $this;
    }

    /**
     * Detach static
     *
     * @return void
     */
    public function detach()
    {
    }

    /**
     * The Entity state getter
     *
     * @return integer
     */
    protected function getEntityState()
    {
        return null;
    }

}
