<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * OrderItem items list
 */
class OrderItem extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    const PARAM_ORDER = 'order';

    /**
     * Order items data (before they are changed)
     *
     * @var array
     */
    protected $orderItemsData = array();

    /**
     * Cached order object
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * PageDataForUpdate runtime cache
     * 
     * @var array
     */
    protected $pageDataForUpdate;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'items_list/model/table/order_item/style.css';
        $list[] = 'change_attribute_values/style.css';
        $list[] = 'product/details/parts/attributes_modify/style.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'items_list/model/table/order_item/controller.js';

        return $list;
    }

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'order_items';
    }

    /**
     * Get data prefix for remove cells
     *
     * @return string
     */
    public function getRemoveDataPrefix()
    {
        return 'delete_order_items';
    }

    /**
     * Process
     *
     * @return void
     */
    public function process()
    {
        if ($this->preValidateAction()) {
            parent::process();

            $items = \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->search($this->getSearchCondition());

            foreach ($items as $item) {
                $item->calculate();
            }

        } else {
            \XLite\Core\TopMessage::getInstance()->addError(static::t('All items cannot be removed from the order.'));
            $this->setActionError();
        }
    }

    /**
     * Return true if order is not editable and items list should be displayed in static mode
     *
     * @return boolean
     */
    protected function isStatic()
    {
        return parent::isStatic() || !\XLite::getController()->isOrderEditable();
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
     * Pre-validate posted data to prevent all order items from the order been deleted.
     * Return true if validation is not detected errors.
     *
     * @return boolean
     */
    protected function preValidateAction()
    {
        $result = true;

        $allItems = $this->getOrder()->getItems();
        $toRemove = $this->getEntityIdListForRemove();
        $newItems = $this->getNewDataLine();

        if (1 >= count($newItems) && count($allItems) === count($toRemove)) {
            $result = false;
        }

        return $result;
    }

    /**
     * Save entities
     */
    protected function saveEntities()
    {
        $this->orderItemsData = $this->getOrderItemsData();

        $data = $this->getRequestData();

        foreach ($this->getPageDataForUpdate() as $entity) {
            $entityId = $entity->getItemId();
            $product = $entity->getProduct();
            if ($product
                && isset($data['order_items'][$entityId]['attribute_values'])
                && is_array($data['order_items'][$entityId]['attribute_values'])
                && $entity->isActualAttributes()
            ) {
                $entity->setAttributeValues(
                    $product->prepareAttributeValues($data['order_items'][$entityId]['attribute_values'])
                );
            }
        }

        $this->postprocessOrderItems(true);

        // Update OrderItems entities
        $count = parent::saveEntities();

        return $count;
    }

    /**
     * Update entities
     *
     * @return void
     */
    protected function updateEntities()
    {
        parent::updateEntities();

        // Register changes in order history
        $this->processOrderItemsChanges($this->orderItemsData, $this->getOrderItemsData());

        // Update stock
        $this->changeAmountInStock();
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
        parent::saveNewEntity($fields, $entity, $line);

        $product = $entity->getProduct();

        if ($product && isset($line['attribute_values']) && is_array($line['attribute_values'])) {
            $entity->setAttributeValues($product->prepareAttributeValues($line['attribute_values']));
        }

        $this->postprocessOrderItems(false, $entity);
    }

    /**
     * Do some actions before save order items
     *
     * @param boolean                $isUpdated True if action item is updated
     * @param \XLite\Model\OrderItem $entity    OrderItem entity
     *
     * @return void
     */
    protected function postprocessOrderItems($isUpdated = false, $entity = null)
    {
        $order = $this->getOrder();

        $order->transformItemsAttributes();
    }

    // {{{ Order changes processing methods

    /**
     * Get order items data fields
     *
     * @return array
     */
    protected function getOrderItemsDataFields()
    {
        return array('name', 'sku', 'price', 'amount', 'attributeValues');
    }

    /**
     * Get order items data
     *
     * @return array
     */
    protected function getOrderItemsData()
    {
        $result = array();
        $fields = $this->getOrderItemsDataFields();
        if ($this->pageDataForUpdate === null) {
            $this->pageDataForUpdate = $this->getPageDataForUpdate();
        }
        foreach ($this->pageDataForUpdate as $entity) {
            $itemData = array();
            foreach ($fields as $field) {
                $getter = 'getOrderItemDataFieldValue' . ucfirst($field);
                if (method_exists($this, $getter)) {
                    $itemData[$field] = $this->$getter($entity);

                } else {
                    $getter = 'get' . ucfirst($field);
                    if (method_exists($entity, $getter)) {
                        $itemData[$field] = $entity->$getter();
                    }
                }
            }
            $result[$entity->getItemId()] = $itemData;
        }

        return $result;
    }

    /**
     * Process order items changes
     *
     * @param array $oldItemsData Array of order items data
     * @param array $newItemsData Changed array of order items data
     *
     * @return void
     */
    protected function processOrderItemsChanges($oldItemsData, $newItemsData)
    {
        $currency = $this->getOrder()->getCurrency();

        foreach ($oldItemsData as $itemId => $data) {
            $changedFields = $this->getChangedFields($itemId, $data, $newItemsData);

            if (!empty($changedFields)) {
                $diff = array_diff($changedFields, array('attributeValues'));

                if (!empty($diff)) {
                    // Order item is changed: register this

                    \XLite\Controller\Admin\Order::setOrderChanges(
                        'Changed items:' . $data['name'],
                        sprintf(
                            '[%s, %s x %d]',
                            $newItemsData[$itemId]['sku'],
                            static::formatPrice($newItemsData[$itemId]['price'], $currency, true),
                            $newItemsData[$itemId]['amount']
                        ),
                        sprintf(
                            '[%s, %s x %d]',
                            $data['sku'],
                            static::formatPrice($data['price'], $currency, true),
                            $data['amount']
                        )
                    );
                }

                if (in_array('attributeValues', $changedFields, true)) {
                    // Order item attributes are changed: register this

                    foreach ($data['attributeValues'] as $attrId => $av) {
                        if ($av['value'] != $newItemsData[$itemId]['attributeValues'][$attrId]['value']) {
                            // Register attribute change
                            \XLite\Controller\Admin\Order::setOrderChanges(
                                'Changed options:' . sprintf('%s [%s]', $data['name'], $av['name']),
                                $newItemsData[$itemId]['attributeValues'][$attrId]['value'],
                                $av['value']
                            );
                        }
                    } // foreach ($oldAttrs...
                }
            } // if (!empty($changedFields)...
        } // foreach ($oldItemsData...
    }

    /**
     * Get changed order item fields
     *
     * @param integer $itemId       Order item ID
     * @param array   $data         Order item old data OPTIONAL
     * @param array   $newItemsData New order items data OPTIONAL
     *
     * @return array
     */
    protected function getChangedFields($itemId, $data = null, $newItemsData = null)
    {
        $changedFields = array();

        if (null === $data) {
            $data = $this->orderItemsData[$itemId];
        }

        if (null === $newItemsData) {
            $newItemsData = $this->getOrderItemsData();
        }

        $fields = $this->getOrderItemsDataFields();

        foreach ($fields as $field) {
            $method = 'isItemDataChanged' . ucfirst($field);

            if (method_exists($this, $method)) {
                // Detect field changes via specific method
                $isChanged = $this->$method($data[$field], $newItemsData[$itemId][$field]);

            } else {
                $isChanged = ($newItemsData[$itemId][$field] != $data[$field]);
            }

            if ($isChanged) {
                $changedFields[] = $field;
            }
        }

        return $changedFields;
    }

    /**
     * Get order item data specific field value
     *
     * @param \XLite\Model\OrderItem $entity OrderItem entity
     *
     * @return array
     */
    protected function getOrderItemDataFieldValueAttributeValues($entity)
    {
        $result = array();

        if ($entity->getAttributeValues()) {
            foreach ($entity->getAttributeValues() as $av) {
                $result[$av->getAttributeId()] = array(
                    'name'  => $av->getActualName(),
                    'value' => $av->getActualValue(),
                );
            }
        }

        return $result;
    }

    /**
     * Get order item data specific field value
     *
     * @param \XLite\Model\OrderItem $entity OrderItem entity
     *
     * @return array
     */
    protected function getOrderItemDataFieldValuePrice($entity)
    {
        return $this->getOrder()->getCurrency()->roundValue($entity->getItemNetPrice());
    }

    /**
     * Return true if attribute values are different
     *
     * @param array $old Old attribute values
     * @param array $new New attribute values
     *
     * @return boolean
     */
    protected function isItemDataChangedAttributeValues($old, $new)
    {
        $isChanged = false;

        foreach ($old as $attrId => $data) {
            if ($new[$attrId]['value'] != $data['value']) {
                $isChanged = true;
                break;
            }
        }

        return $isChanged;
    }

    // }}}

    /**
     * Change products quantity in stock if needed
     *
     * @return void
     */
    protected function changeAmountInStock()
    {
        if ($this->isNeedUpdateStock()) {
            foreach ($this->getPageDataForUpdate() as $entity) {
                if (!$entity->isDeleted()) {
                    $this->changeItemAmountInStock($entity);
                }
            }
        }
    }

    /**
     * Change product quantity in stock
     *
     * @param \XLite\Model\OrderItem $entity Order item entity
     *
     * @return void
     */
    protected function changeItemAmountInStock($entity)
    {
        if (isset($this->orderItemsData[$entity->getItemId()]['amount'])
            && $entity->getAmount() != $this->orderItemsData[$entity->getItemId()]['amount']
        ) {
            // Calculate amount to update stock: negative when qty was increased and positive when decreased
            $delta = $this->orderItemsData[$entity->getItemId()]['amount'] - $entity->getAmount();

            // Update stock
            $entity->changeAmount($delta);
        }
    }

    /**
     * Return true if stock need to be updated after order items changed
     *
     * @return boolean
     */
    protected function isNeedUpdateStock()
    {
        static $result;

        if (null === $result) {
            $result = \XLite\Controller\Admin\Order::isNeedProcessStock();

            if ($result) {
                $order = $this->getOrder();

                $property = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Property')->findOneBy(
                    array(
                        'paymentStatus'  => $order->getPaymentStatus(),
                        'shippingStatus' => $order->getShippingStatus(),
                    )
                );

                $result = !($property ? $property->getIncStock() : false);
            }
        }

        return $result;
    }

    /**
     * Return entity attribute values as a single string (e.g. Color: Red, Size: XXL, ...)
     *
     * @param \XLite\Model\OrderItem $entity OrderItem entity
     *
     * @return string
     */
    protected function getAttributeValuesAsString($entity)
    {
        $result = array();

        foreach ($entity->getAttributeValues() as $av) {
            $result[] = sprintf('%s: %s', $av->getActualName(), $av->getActualValue());
        }

        return implode(', ', $result);
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
        $result = parent::postprocessInsertedEntity($entity, $line);

        if ($this->isNeedUpdateStock()) {
            // Get items from stock
            $entity->changeAmount(-1 * $entity->getAmount());
        }

        $product = $entity->getProduct();
        $attributes = $this->getAttributeValuesAsString($entity);

        \XLite\Controller\Admin\Order::setOrderChanges(
            'Added items:' . $entity->getItemId(),
            sprintf(
                '[%s] %s (%s x %d%s)',
                $product->getSku(),
                $product->getName(),
                static::formatPrice($entity->getPrice(), $entity->getOrder()->getCurrency(), true),
                $entity->getAmount(),
                $attributes ? ', ' . $attributes : ''
            )
        );

        return $result;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, 'XLite\Model\Order'),
        );
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        if (null === $this->order) {
            $order = $this->getParam(static::PARAM_ORDER);

            // Get temporary order if exists otherwise get current order
            $this->order = \XLite\Controller\Admin\Order::getTemporaryOrder($order->getOrderId(), false) ?: $order;
        }

        return $this->order;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME         => static::t('Item'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Select\Model\Product\OrderItem',
                static::COLUMN_TEMPLATE     => 'items_list/model/table/order_item/cell.name.twig',
                static::COLUMN_PARAMS       => array(
                    'required' => true,
                    \XLite\View\FormField\Select\Model\OrderItemSelector::PARAM_ORDER_ID => $this->getOrder()->getOrderId(),
                ),
                static::COLUMN_MAIN         => true,
                static::COLUMN_ORDERBY      => 100,
            ),
            'price' => array(
                static::COLUMN_NAME     => static::t('Price'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text\Price\OrderItemPrice',
                static::COLUMN_PARAMS   => array(
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN              => 0,
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_CTRL => false,
                ),
                static::COLUMN_ORDERBY  => 200,
            ),
            'amount' => array(
                static::COLUMN_NAME    => static::t('Qty'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Integer\OrderItemAmount',
                static::COLUMN_PARAMS  => array('required' => true),
                static::COLUMN_ORDERBY => 300,
            ),
            'total' => array(
                static::COLUMN_NAME     => static::t('Total'),
                static::COLUMN_TEMPLATE => 'items_list/model/table/order_item/cell.total.twig',
                static::COLUMN_CREATE_TEMPLATE => 'items_list/model/table/order_item/cell.total.twig',
                static::COLUMN_ORDERBY  => 400,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\OrderItem';
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add product';
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_BOTTOM;
    }


    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' order-items';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
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
        $result = parent::getLineAttributes($index, $entity);

        if ($entity) {
            $result['data-clear-price'] = $entity->getPrice();
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
        $classes = parent::defineLineClass($index, $entity);

        $classes[] = (!$entity || $this->isAutoItem($entity, 'price'))
            ? 'ctrl-auto'
            : 'ctrl-manual';

        if ($entity && $entity->isPersistent()) {
            if ($entity->getSubtotal() <= 0) {
                $classes[] = 'zero-total';
            }

            if ($this->isPriceControlledServer($entity)) {
                $classes[] = 'server-price-control';
            }
        }

        return $classes;
    }

    

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->order = $this->getOrder();

        return $result;
    }

    // }}}

    /**
     * Correct request data: detect temporary order and add data for temporary order
     *
     * @return array
     */
    protected function getRequestData()
    {
        $data = parent::getRequestData();

        $orderId = $this->getOrder()->getOrderId();
        $tmpOrderData = \XLite\Controller\Admin\Order::getTemporaryOrderData();

        $tmpItems = array();

        if (empty($tmpOrderData[$orderId])) {
            // Current order is not a source order - search for temporary order in cache

            foreach ($tmpOrderData as $oid => $tmpData) {
                $tmpOid = is_object($tmpData['order']) ? $tmpData['order']->getOrderId() : (int) $tmpData['order'];

                if ($tmpOid == $orderId) {
                    $tmpItems = isset($tmpData['items']) ? $tmpData['items'] : array();
                    break;
                }
            }
        }

        if (!empty($tmpItems)) {
            $dataTypes = array(
                $this->getDataPrefix(),
                $this->getRemoveDataPrefix(),
            );

            // Add data for temporary order
            foreach ($tmpItems as $origId => $tmpId) {
                foreach ($dataTypes as $dataType) {
                    if (!empty($data[$dataType][$origId])) {
                        $data[$dataType][$tmpId] = $data[$dataType][$origId];
                        unset($data[$dataType][$origId]);
                    }
                }
            }

            $this->requestData = $data;
        }

        return $this->requestData;
    }

    /**
     * Create order item entity
     *
     * @return \XLite\Model\OrderItem
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();
        $entity->setOrder($this->getOrder());
        $this->getOrder()->addItems($entity);

        return $entity;
    }

    /**
     * Undo new entity creation
     *
     * @param \XLite\Model\OrderItem $entity Order item entity
     *
     * @return void
     */
    protected function undoCreatedEntity($entity)
    {
        $this->getOrder()->getItems()->removeElement($entity);

        parent::undoCreatedEntity($entity);
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
        $product = $entity->getProduct();

        if ($this->isNeedUpdateStock() && !$entity->isDeleted()) {
            // Return items to stock
            $entity->changeAmount($entity->getAmount());
        }

        $attributes = $this->getAttributeValuesAsString($entity);

        \XLite\Controller\Admin\Order::setOrderChanges(
            'Removed items:' . $entity->getItemId(),
            sprintf(
                '[%s] %s (%s x %d%s)',
                $product->getSku(),
                $product->getName(),
                static::formatPrice($entity->getPrice(), $entity->getOrder()->getCurrency(), true),
                $entity->getAmount(),
                $attributes ? ', ' . $attributes : ''
            )
        );

        return parent::removeEntity($entity);
    }

    /**
     * Check - item is auto-controller or not
     *
     * @param \XLite\Model\OrderItem $item Order item
     * @param string                 $part Item part
     *
     * @return boolean
     */
    protected function isAutoItem(\XLite\Model\OrderItem $item, $part)
    {
        $data = \XLite\Core\Request::getInstance()->auto;

        return empty($data['items'])
            || empty($data['items'][$item->getItemId()])
            || !empty($data['items'][$item->getItemId()][$part]);
    }

    /**
     * Get original price
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return float
     */
    protected function getOriginalPrice(\XLite\Model\OrderItem $item)
    {
        return $item->getPrice();
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
        return null;
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return null;
    }

    /**
     * Get JS handler class name (used for pagination)
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'OrderItemsList';
    }

    /**
     * Pre-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        return $this->validateOrderItem($entity, false);
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
        return $this->validateOrderItem($entity, true);
    }

    /**
     * Validate order item
     *
     * @param \XLite\Model\AEntity $entity   Entity
     * @param boolean              $isUpdate True if entity is updated; false if created
     *
     * @return boolean
     */
    protected function validateOrderItem(\XLite\Model\AEntity $entity, $isUpdate = false)
    {
        $result = true;

        if ($isUpdate && !$this->getChangedFields($entity->getItemId())) {
            // Ignore order item validation if item was updated but was not changed

        } elseif (!$entity->isActualAttributes()) {
            $data = $this->getRequestData();
            if (isset($data['order_items'][$entity->getItemId()]['attribute_values'])) {
                $message = static::t('Order item attributes are out-of-date and cannot be edited');
            }

        } elseif (!$this->isValidEntity($entity)) {
            $result = false;
            $message = static::t('Product with selected properties cannot be purchased');

        } elseif (!$isUpdate && $entity->hasWrongAmount()) {
            $result = false;
            $message = static::t(
                'The specified amount of product exceeds maximum amount of product in stock',
                array(
                    'value' => $entity->getAmount(),
                    'max'   => $entity->getProductAvailableAmount(),
                )
            );

        } elseif ($isUpdate && $entity->isItemHasWrongAmount($entity)) {
            $result = false;
            $message = static::t(
                'The specified amount of product exceeds maximum amount of product in stock',
                array(
                    'value' => $entity->getAmount(),
                    'max'   => $entity->getProductAvailableAmount() + $this->orderItemsData[$entity->getItemId()]['amount'],
                )
            );
        }

        if (!$result) {
            $this->errorMessages[] = sprintf(
                '[%s] %s: %s',
                static::t('Error'),
                $message,
                $this->formatItem($entity)
            );

        } elseif (!empty($message)) {
            $this->warningMessages[] = sprintf(
                '[%s] %s: %s',
                static::t('Warning'),
                $message,
                $this->formatItem($entity)
            );
        }

        return $result;
    }

    /**
     * Check order item and return true if this is valid
     *
     * @param \XLite\Model\OrderItem $entity Order item entity
     *
     * @return boolean
     */
    protected function isValidEntity($entity)
    {
        $result = 0 < $entity->getAmount();

        if ($result
            && ($entity->hasAttributeValues()
                || $entity->getProduct()->hasEditableAttributes()
            )
        ) {
            $result = array_keys($entity->getAttributeValuesIds()) == $entity->getProduct()->getEditableAttributesIds();
        }

        return $result;
    }

    /**
     * Return false if order item amount exceeds maximum allowed value
     *
     * @param \XLite\Model\OrderItem $entity Order item entity
     *
     * @return boolean
     */
    protected function isItemHasWrongAmount($entity)
    {
        $oldAmount = $this->orderItemsData[$entity->getItemId()]['amount'];

        $maxAmount = $entity->getProductAvailableAmount();

        return $oldAmount + $maxAmount < $entity->getAmount();
    }

    /**
     * Get formatted description of order item
     *
     * @param \XLite\Model\AEntity $entity Order item entity
     *
     * @return string
     */
    protected function formatItem(\XLite\Model\AEntity $entity)
    {
        $attributes = $this->getAttributeValuesAsString($entity);

        return sprintf(
            '[%s] %s (%s x %d%s)',
            $entity->getSku(),
            $entity->getName(),
            static::formatPrice($entity->getPrice(), $entity->getOrder()->getCurrency(), true),
            $entity->getAmount(),
            $attributes ? ', ' . $attributes : ''
        );
    }
}
