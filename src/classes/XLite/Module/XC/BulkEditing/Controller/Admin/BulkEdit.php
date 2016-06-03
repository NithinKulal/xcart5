<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Controller\Admin;

use XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario;

class BulkEdit extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    protected $scenario;

    protected $generator;

    /**
     * params
     *
     * @var string
     */
    protected $params = ['target', 'scenario'];

    /**
     * Returns object to get initial data and populate submitted data to
     *
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        $dto = Scenario::getScenarioDTO($this->scenario);

        return $dto ? new $dto : null;
    }

    /**
     * Store 'scenario' request param
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $this->scenario = \XLite\Core\Request::getInstance()->scenario;
    }

    public function getCurrentScenario()
    {
        return $this->scenario;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $scenario = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioData($this->scenario);

        return $scenario['title'];
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Request::getInstance()->completed) {
            \XLite\Core\TopMessage::addInfo('BulkEdit success');
            $this->setReturnURL($this->buildURL('bulk_edit', '', ['scenario' => $this->scenario]));

        } elseif (\XLite\Core\Request::getInstance()->failed) {
            \XLite\Core\TopMessage::addError('BulkEdit error');
            $this->setReturnURL($this->buildURL('bulk_edit', '', ['scenario' => $this->scenario]));
        }
    }

    /**
     * Before bulk edit form
     */
    protected function doActionStart()
    {
        $selected = \XLite\Core\Request::getInstance()->select;
        $selected = $selected ? array_keys($selected) : null;

        $conditionCell = null;
        if (null === $selected) {
            $itemList = \XLite\Core\Request::getInstance()->itemsList;
            if (class_exists($itemList) && method_exists($itemList, 'getConditionCellName')) {
                $conditionCell = $itemList::getConditionCellName();
            }
        }

        $sessionCellName = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
        \XLite\Core\Session::getInstance()->{$sessionCellName} = [
            'selected'      => $selected,
            'conditionCell' => $conditionCell,
        ];

        $this->setReturnURL($this->buildURL('bulk_edit', '', ['scenario' => $this->scenario]));
    }

    /**
     * Bulk edit form submit
     */
    protected function doActionBulkEdit()
    {
        /** @var \XLite\Module\XC\BulkEditing\Model\DTO\Product\AProduct $dto */
        $dto = $this->getFormModelObject();

        $formModel = null;
        $formModelClass = Scenario::getScenarioFormModel($this->scenario);
        if ($formModelClass) {
            $formModel = new $formModelClass(['object' => $dto]);
        }

        if ($formModel) {
            $form = $formModel->getForm();
            $data = \XLite\Core\Request::getInstance()->getData();

            $form->submit($data[$this->formName]);

            $dto->setEditedFields(json_decode($data['bulk_edit']));

            if ($form->isValid()) {
                $sessionCellName = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
                \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator::run(
                    [
                        'data'      => serialize($dto),
                        'scenarios' => [Scenario::getScenarioStep($this->scenario)],
                        'filter'    => \XLite\Core\Session::getInstance()->{$sessionCellName},
                    ]
                );
            } else {
                \XLite\Core\Session::getInstance()->{$this->formModelDataSessionCellName} = $data[$this->formName];
            }
        }
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->scenario;
    }

    /**
     * @return \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator
     */
    public function getGenerator()
    {
        if (!isset($this->generator)) {
            $eventName = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator::getEventName();
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);
            $this->generator = ($state && isset($state['options']))
                ? new \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator($state['options'])
                : false;
        }

        return $this->generator;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isBulkEditNotFinished()
    {
        $eventName = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS],
            true
        )
        && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(
            \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator::getBulkEditCancelFlagVarName()
        );
    }
}
