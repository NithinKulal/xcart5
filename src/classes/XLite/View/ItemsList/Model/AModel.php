<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Abstract admin model-based items list
 */
abstract class AModel extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Sortable types
     */
    const SORT_TYPE_NONE  = 0;
    const SORT_TYPE_MOVE  = 1;
    const SORT_TYPE_INPUT = 2;

    /**
     * Create inline position
     */
    const CREATE_INLINE_NONE   = 0;
    const CREATE_INLINE_TOP    = 1;
    const CREATE_INLINE_BOTTOM = 2;


    /**
     * Hightlight step
     *
     * @var integer
     */
    protected $hightlightStep = 2;

    /**
     * Error messages
     *
     * @var array
     */
    protected $errorMessages = array();

    /**
     * Warning messages
     *
     * @var array
     */
    protected $warningMessages = array();

    /**
     * Request data
     *
     * @var array
     */
    protected $requestData;

    /**
     * Entities created by $this::processCreate
     *
     * @var array
     */
    protected $createdEntities = [];

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (in_array($this->getSortableType(), [static::SORT_TYPE_MOVE, static::SORT_TYPE_INPUT], true)) {
            unset($this->widgetParams[static::PARAM_SORT_BY]);
        }
    }

    // {{{ Fields

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'data';
    }

    /**
     * Get data prefix for remove cells
     *
     * @return string
     */
    public function getRemoveDataPrefix()
    {
        return 'delete';
    }

    /**
     * Get data prefix for select cells
     *
     * @return string
     */
    public function getSelectorDataPrefix()
    {
        return 'select';
    }

    /**
     * Get data prefix for new data
     *
     * @return string
     */
    public function getCreateDataPrefix()
    {
        return 'new';
    }

    /**
     * Return list of created entities
     *
     * @return array
     */
    public function getCreatedEntities()
    {
        return $this->createdEntities;
    }

    /**
     * Get self
     *
     * @return \XLite\View\ItemsList\Model\AModel
     */
    protected function getSelf()
    {
        return $this;
    }

    // }}}

    // {{{ Model processing

    /**
     * Get field objects list (only inline-based form fields)
     *
     * @return array
     */
    abstract protected function getFieldObjects();

    /** @todo: remove before commit */
    ///**
    // * Define repository name
    // *
    // * @return string
    // */
    //abstract protected function defineRepositoryName();

    /**
     * Quick process
     *
     * @param array $parameters Parameters OPTIONAL
     *
     * @return void
     */
    public function processQuick(array $parameters = array())
    {
        $this->setWidgetParams($parameters);
        $this->init();
        $this->process();
    }

    /**
     * Process
     *
     * @return void
     */
    public function process()
    {
        $this->processUpdate();
        $this->processRemove();
        $this->processCreate();

        \XLite\Core\Database::getEM()->flush();
    }

    // {{{ Create

    /**
     * Get create field classes
     *
     * @return array
     */
    protected function getCreateFieldClasses()
    {
        return array();
    }

    /**
     * Process create new entities
     *
     * @return void
     */
    protected function processCreate()
    {
        $errCount = 0;
        $count = 0;

        foreach ($this->getNewDataLine() as $key => $line) {
            if ($this->isNewLineSufficient($line, $key)) {
                $entity = $this->createEntity();
                $fields = $this->createInlineFields($line, $entity);

                if ($this->validateNewEntity($fields, $key)) {
                    $this->saveNewEntity($fields, $entity, $line);
                    if ($this->prevalidateNewEntity($entity)) {
                        $this->insertNewEntity($entity);
                        $this->postprocessInsertedEntity($entity, $line);
                        $this->createdEntities[] = $entity;
                        $count++;

                    } else {
                        $this->undoCreatedEntity($entity);
                        $errCount++;
                    }

                } else {
                    $this->undoCreatedEntity($entity);
                    $errCount++;
                }
            }
        }

        if (0 < $count) {
            $label = $this->getCreateMessage($count);
            if ($label) {
                \XLite\Core\TopMessage::getInstance()->addInfo($label);
            }
        }

        if (0 < $errCount) {
            $this->processCreateErrors();
        }

        $this->processCreateWarnings();
    }

    /**
     * Validate new entity
     *
     * @param array  $fields Fields list
     * @param string $key    Field key
     *
     * @return boolean
     */
    protected function validateNewEntity(array $fields, $key)
    {
        $validated = 0 < count($fields);
        foreach ($fields as $inline) {
            $validated = $this->validateCell($inline, $key) && $validated;
        }

        return $validated;
    }

    /**
     * Save new entity
     *
     * @param array                $fields Fields
     * @param \XLite\Model\AEntity $entity Entity object
     * @param array                $line   New entity data from request
     *
     * @return void
     */
    protected function saveNewEntity(array $fields, $entity, $line)
    {
        foreach ($fields as $inline) {
            $this->saveCell($inline);
        }
    }

    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    /**
     * Insert new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        $entity->getRepository()->insert($entity);
    }

    /**
     * Postprocess inserted entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $line   Array of entity data from request
     *
     * @return boolean
     */
    protected function postprocessInsertedEntity(\XLite\Model\AEntity $entity, array $line)
    {
        return true;
    }

    /**
     * Undo created entity
     *
     * @param \XLite\Model\AEntity $entity Created entity
     *
     * @return void
     */
    protected function undoCreatedEntity($entity)
    {
        \XLite\Core\Database::getEM()->remove($entity);
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return static::t('X entities has been created', array('count' => $count));
    }

    /**
     * Get update message
     *
     * @return string
     */
    protected function getUpdateMessage()
    {
        return null;
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entityClass = $this->defineRepositoryName();

        return new $entityClass;
    }

    /**
     * Get dump entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDumpEntity()
    {
        return $this->executeCachedRuntime(function () {
            return $this->createEntity();
        });
    }

    /**
     * Get new data line
     *
     * @return array
     */
    protected function getNewDataLine()
    {
        $data = $this->getRequestData();
        $prefix = $this->getCreateDataPrefix();

        return (isset($data[$prefix]) && is_array($data[$prefix])) ? $data[$prefix] : array();
    }

    /**
     * Check - new line is sufficient or not
     *
     * @param array   $line Data line
     * @param integer $key  Field key gathered from request data, eg: new[this-key][field-name]
     *                      (see ..\AInline::processCreate())
     *
     * @return boolean
     */
    protected function isNewLineSufficient(array $line, $key)
    {
        return 0 !== $key && 0 < count($line);
    }

    /**
     * Create inline fields list
     *
     * @param array                $line   Line data
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function createInlineFields(array $line, \XLite\Model\AEntity $entity)
    {
        $list = array();

        foreach ($this->getCreateFieldClasses() as $object) {
            $this->prepareInlineField($object, $entity);
            $list[] = $object;
        }

        return $list;
    }

    /**
     * Process errors
     *
     * @return void
     */
    protected function processCreateErrors()
    {
        \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);

        // Run controller's method
        $this->setActionError();
    }

    /**
     * Process warnings
     *
     * @return void
     */
    protected function processCreateWarnings()
    {
        $warnings = $this->getWarningMessages();

        if ($warnings) {
            \XLite\Core\TopMessage::getInstance()->addBatch($warnings, \XLite\Core\TopMessage::WARNING);
        }
    }


    // }}}

    // {{{ Remove

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X entities has been removed', array('count' => $count));
    }

    /**
     * Process remove
     *
     * @return integer
     */
    protected function processRemove()
    {
        $count = 0;

        foreach ($this->getEntityIdListForRemove() as $id) {
            $entity = $this->findForRemove($id);
            if ($entity && $this->removeEntity($entity)) {
                $count++;
            }
        }

        if (0 < $count) {
            \XLite\Core\Database::getEM()->flush();

            $label = $this->getRemoveMessage($count);
            if ($label) {
                \XLite\Core\TopMessage::getInstance()->addInfo($label);
            }
        }

        return $count;
    }

    /**
     * Find for remove
     *
     * @param mixed $id Entity id
     *
     * @return \XLite\Model\AEntity
     */
    protected function findForRemove($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Get entity's ID list for remove
     *
     * @return array
     */
    protected function getEntityIdListForRemove()
    {
        $data = $this->getRequestData();
        $prefix = $this->getRemoveDataPrefix();

        $list = array();

        if (isset($data[$prefix]) && is_array($data[$prefix]) && $data[$prefix]) {
            foreach ($data[$prefix] as $id => $allow) {
                if ($allow) {
                    $list[] = $id;
                }
            }
        }

        return $list;
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $entity->getRepository()->delete($entity, false);

        return true;
    }

    // }}}

    // {{{ Update

    /**
     * Process update
     *
     * @return boolean
     */
    protected function processUpdate()
    {
        $result = true;

        if ($this->isActiveModelProcessing()) {
            $result = $this->validateUpdate() && false !== $this->update();

            if (!$result) {
                $this->processUpdateErrors();
            } else {
                $label = $this->getUpdateMessage();
                if ($label) {
                    \XLite\Core\TopMessage::getInstance()->addInfo($label);
                }
            }

            $this->processUpdateWarnings();
        }

        return $result;
    }

    /**
     * Check - model processing is active or not
     *
     * @return boolean
     */
    protected function isActiveModelProcessing()
    {
        return $this->hasResults() && $this->getFieldObjects();
    }

    /**
     * Validate data
     *
     * @return boolean
     */
    protected function validateUpdate()
    {
        $validated = true;

        foreach ($this->prepareInlineFields() as $field) {
            $validated = $this->validateCell($field) && $validated;
        }

        return $validated;
    }

    /**
     * Save data
     *
     * @return integer
     */
    protected function update()
    {
        $count = $this->saveEntities();

        if ($this->prevalidateEntities()) {
            $this->updateEntities();

        } else {
            $this->undoEntities();
            $count = false;
        }

        return $count;
    }

    /**
     * Save entities
     *
     * @return integer
     */
    protected function saveEntities()
    {
        $count = 0;

        foreach ($this->prepareInlineFields() as $field) {
            $count++;
            $this->saveCell($field);
        }

        return $count;
    }

    /**
     * Pre-validate entities
     *
     * @return boolean
     */
    protected function prevalidateEntities()
    {
        $result = true;
        foreach ($this->getPageDataForUpdate() as $entity) {
            $result = $this->prevalidateEntity($entity) && $result;
        }

        return $result;
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    /**
     * Undo entities if entities pre-validation routine detect some errors
     *
     * @return void
     */
    protected function undoEntities()
    {
        foreach ($this->getPageDataForUpdate() as $entity) {
            \XLite\Core\Database::getEM()->refresh($entity);
        }
    }

    /**
     * Update entities
     *
     * @return void
     */
    protected function updateEntities()
    {
        foreach ($this->getPageDataForUpdate() as $entity) {
            $entity->getRepository()->update($entity, array(), false);
            if ($this->isDefault()) {
                $entity->setDefaultValue($this->isDefaultEntity($entity));
            }
        }
    }

    /**
     * Is default entity
     *
     * @param \XLite\Model\AEntity $entity Line
     *
     * @return boolean
     */
    protected function isDefaultEntity(\XLite\Model\AEntity $entity)
    {
        $requestData = $this->getRequestData();

        return isset($requestData['defaultValue']) && (int) $requestData['defaultValue'] === $entity->getUniqueIdentifier();
    }

    /**
     * Process errors
     *
     * @return void
     */
    protected function processUpdateErrors()
    {
        \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);

        // Run controller's method
        $this->setActionError();
    }

    /**
     * Process errors
     *
     * @return void
     */
    protected function processUpdateWarnings()
    {
        $warnings = $this->getWarningMessages();

        if ($warnings) {
            \XLite\Core\TopMessage::getInstance()->addBatch($warnings, \XLite\Core\TopMessage::WARNING);
        }
    }

    /**
     * Validate inline field
     *
     * @param \XLite\View\FormField\Inline\AInline $inline Inline field
     * @param integer                              $key    Field key gathered from request data,
     *                                                     eg: new[this-key][field-name]
     *                                                     (see ..\AInline::processCreate()) OPTIONAL
     *
     * @return boolean
     */
    protected function validateCell(\XLite\View\FormField\Inline\AInline $inline, $key = null)
    {
        $inline->setValueFromRequest($this->getRequestData(), $key);
        list($flag, $message) = $inline->validate();
        if (!$flag) {
            $this->addErrorMessage($inline, $message);
        }

        return $flag;
    }

    /**
     * Save cell
     *
     * @param \XLite\View\FormField\Inline\AInline $inline Inline field
     *
     * @return void
     */
    protected function saveCell(\XLite\View\FormField\Inline\AInline $inline)
    {
        $inline->saveValue();
    }

    /**
     * Get inline fields
     *
     * @return array
     */
    protected function prepareInlineFields()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineInlineFields();
        });
    }

    /**
     * Define inline fields
     *
     * @return array
     */
    protected function defineInlineFields()
    {
        $list = array();

        foreach ($this->getPageDataForUpdate() as $entity) {
            foreach ($this->getFieldObjects() as $object) {
                $this->prepareInlineField($object, $entity);
                $list[] = $object;
            }
        }

        return $list;
    }

    /**
     * Get inline field
     *
     * @param \XLite\View\FormField\Inline\AInline $field  Field
     * @param \XLite\Model\AEntity                 $entity Entity
     *
     * @return void
     */
    protected function prepareInlineField(\XLite\View\FormField\Inline\AInline $field, \XLite\Model\AEntity $entity)
    {
        $field->setWidgetParams(array('entity' => $entity, 'itemsList' => $this));
    }

    /**
     * Get page data for update
     *
     * @return array
     */
    protected function getPageDataForUpdate()
    {
        $list = array();
        foreach ($this->getPageData() as $entity) {
            if ($entity->isPersistent()) {
                $list[] = $entity;
            }
        }

        return $list;
    }

    // }}}

    // {{{ Misc.

    /**
     * Get request data
     *
     * @return array
     */
    protected function getRequestData()
    {
        if (null === $this->requestData) {
            $this->requestData = $this->defineRequestData();
        }

        return $this->requestData;
    }

    /**
     * Define request data
     *
     * @return array
     */
    protected function defineRequestData()
    {
        return $this->prepareRequestParamsList();
    }

    /**
     * Add error message
     *
     * @param \XLite\View\FormField\Inline\AInline $inline  Inline field
     * @param string                               $message Message
     */
    protected function addErrorMessage(\XLite\View\FormField\Inline\AInline $inline, $message)
    {
        $this->errorMessages[] = $inline->getLabel() . ': ' . $message;
    }

    /**
     * Add error message
     *
     * @param        $label
     * @param string $message Message
     */
    protected function addPlainErrorMessage($label, $message)
    {
        $this->errorMessages[] = ($label ? $label . ': ' : '') . $message;
    }

    /**
     * Get error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Add error message
     *
     * @param \XLite\View\FormField\Inline\AInline $inline  Inline field
     * @param string                               $message Message
     *
     * @return void
     */
    protected function addWarningMessage(\XLite\View\FormField\Inline\AInline $inline, $message)
    {
        $this->warningMessages[] = $inline->getLabel() . ': ' . $message;
    }

    /**
     * Get warning messages
     *
     * @return array
     */
    protected function getWarningMessages()
    {
        return $this->warningMessages;
    }

    // }}}

    // {{{ Content helpers

    /**
     * Get anchor name
     *
     * @return string
     */
    public function getAnchorName()
    {
        return implode('_', $this->getViewClassKeys());
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/model/style.css';

        return $list;
    }

    /**
     * Check - body tempalte is visible or not
     *
     * @return boolean
     */
    protected function isPageBodyVisible()
    {
        return $this->hasResults() || static::CREATE_INLINE_NONE !== $this->isInlineCreation();
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->isPageBodyVisible() && $this->getPager();
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'model';
    }

    /**
     * Get line attributes
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line entity OPTIONAL
     *
     * @return array
     */
    protected function getLineAttributes($index, \XLite\Model\AEntity $entity = null)
    {
        $result = array(
            'class'   => $this->defineLineClass($index, $entity),
            'data-id' => $entity ? $entity->getUniqueIdentifier() : 0,
        );

        if (-1 == $index) {
            $result['style'] = 'display: none;';
        }

        return $result;
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = array('line');

        if (0 === $index) {
            $classes[] = 'first';
        }

        if ($this->getItemsCount() == $index + 1) {
            $classes[] = 'last';
        }

        if (0 === ($index + 1) % $this->hightlightStep) {
            $classes[] = 'even';
        }

        if ($entity && $entity->isPersistent()) {
            $classes[] = 'entity-' . $entity->getUniqueIdentifier();

        } else {
            $classes[] = 'create-tpl';
            $classes[] = 'dump-entity';
        }

        return $classes;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.' . implode('.', $this->getListNameSuffixes());
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        $parts = explode('\\', get_called_class());

        $names = array();
        if ('Module' === $parts[1]) {
            $names[] = strtolower($parts[2]);
            $names[] = strtolower($parts[3]);
        }

        $names[] = strtolower($parts[count($parts) - 1]);

        return $names;
    }

    /**
     * Search for page data. Returns entity if search count has single result or null otherwise.
     *
     * @return array|null
     */
    public function searchForSingleEntity()
    {
        $result = null;

        if (1 == $this->getItemsCount()) {
            $data = $this->getPageData();
            $result = current($data);
        }

        return $result;
    }

    /**
     * Build entity page URL
     * @todo: reorder params
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            array($entity->getUniqueIdentifierName() => $entity->getUniqueIdentifier())
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'widget items-list'
            . ' widgetclass-' . $this->getWidgetClass()
            . ' widgettarget-' . static::getWidgetTarget()
            . ' sessioncell-' . $this->getSessionCell();
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        return array(
            'class'         => $this->getContainerClass(),
            'data-js-class' => $this->getJSHandlerClassName(),
        );
    }

    /**
     * Get container attributes as string
     *
     * @return string
     */
    protected function getContainerAttributesAsString()
    {
        $list = array();
        foreach ($this->getContainerAttributes() as $name => $value) {
            $list[] = $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return implode(' ', $list);
    }


    // }}}

    // {{{ Line behaviors

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list item as default
     *
     * @return boolean
     */
    protected function isDefault()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return null;
    }

    /**
     * Get edit link
     *
     * @return string
     */
    protected function getEditLink()
    {
        return null;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Create';
    }

    /**
     * Get entity position
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return integer
     */
    protected function getEntityPosition(\XLite\Model\AEntity $entity)
    {
        return $entity->getOrder();
    }

    // }}}

    // {{{ Sticky panel

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return $this->getPanelClass();
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsListForm';
    }

    // }}}
}
