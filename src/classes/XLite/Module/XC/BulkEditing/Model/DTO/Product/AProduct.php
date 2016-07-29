<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Model\DTO\Product;

use XLite\Model\DTO\Base\CommonCell;

/**
 * Class Categories
 */
abstract class AProduct extends \XLite\Model\DTO\Base\ADTO
{
    protected $editedFields = [];
    protected $scenario = '';

    public function setEditedFields($fields)
    {
        $this->editedFields = array_map(function ($item) {
            return preg_replace('/^[^.]+\./', '', $item);
        }, $fields);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $fields = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioFields($this->scenario);
        foreach ($fields as $section => $sectionFields) {
            foreach ($sectionFields as $name => $field) {
                if ($this->isEdited($section . '.' . $name)) {
                    call_user_func([$field['class'], 'populateData'], $name, $object, $this->{$section});
                }
            }
        }
    }

    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        $fields = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioFields($this->scenario);

        foreach ($fields as $section => $sectionFields) {
            $sectionData = [];
            foreach ($sectionFields as $name => $field) {
                $sectionData = array_merge($sectionData, call_user_func([$field['class'], 'getData'], $name, $object));
            }
            $this->{$section} = new CommonCell($sectionData);
        }
    }

    protected function isEdited($fieldPath)
    {
        return in_array($fieldPath, $this->editedFields, true);
    }
}
