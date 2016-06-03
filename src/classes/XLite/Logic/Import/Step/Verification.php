<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Step;

/**
 * Verification step 
 */
class Verification extends \XLite\Logic\Import\Step\Base\DataStep
{

    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Verified');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Verifying data before importing...');
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        return $this->getProcessor()->processCurrentRow(\XLite\Logic\Import\Processor\AProcessor::MODE_VERIFICATION);
    }

    /**
     * Check - step's work has been done or not
     *
     * @return boolean
     */
    public function isStepDone()
    {
        $result = parent::isStepDone();

        if ($result && $this->isCurrentStep()) {
            $columnsMetaData = $this->getOptions()->columnsMetaData;

            $count = 0;
            foreach ($columnsMetaData as $v) {
                if (isset($v['count'])) {
                    $count =+ $v['count'];
                    if ($count > 0) {
                        break;
                    }
                }
            }

            $result = $count > 0;
        }

        return $result;
    }

    /**
     * Get error language label
     *
     * @return array
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Lines verified: X out of Y with errors',
            array(
                'X'      => $options->position,
                'Y'      => $options->rowsCount,
                'errors' => $options->errorsCount,
                'warns'  => $options->warningsCount,
            )
        );
    }

    /**
     * Get normal language label
     *
     * @return array
     */
    public function getNormalLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Lines checked: X out of Y',
            array(
                'X' => $options->position,
                'Y' => $options->rowsCount,
            )
        );
    }

}

