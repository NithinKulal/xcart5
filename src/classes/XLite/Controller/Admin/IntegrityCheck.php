<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Class IntegrityCheck
 */
class IntegrityCheck extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Integrity check');
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isCheckProcessNotFinished()
    {
        $eventName = \XLite\Logic\IntegrityCheck\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                array(\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS)
            )
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(
                \XLite\Logic\IntegrityCheck\Generator::getCancelFlagVarName()
            );
    }

    /**
     * @inheritDoc
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'start'
            ]
        );
    }


    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStart()
    {
        \XLite\Logic\IntegrityCheck\Generator::run([
            'steps' => [
                'core',
                'modules',
            ]
        ]);
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionIntegrityCheckCancel()
    {
        \XLite\Logic\IntegrityCheck\Generator::cancel();

        $this->setReturnURL('integrity_check');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->process_completed) {
            \XLite\Core\TopMessage::addInfo('Integrity check has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('integrity_check')
            );

        } elseif ($request->process_failed) {
            \XLite\Core\TopMessage::addError('Integrity check has been stopped.');

            $this->setReturnURL(
                $this->buildURL('integrity_check')
            );
        }
    }

}