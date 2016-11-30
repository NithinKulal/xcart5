<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\IntegrityCheck;

/**
 * Class Result
 */
class Result extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = array(
            'file'  => 'integrity_check/result.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return 'integrity_check';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/result.twig'; 
    }

    /**
     * @return array
     */
    public function getFilesGroups()
    {
        $coreData = \XLite\Core\TmpVars::getInstance()->integrityCheckCoreData ?: [];
        $modulesData = \XLite\Core\TmpVars::getInstance()->integrityCheckModulesData ?: [];

        uasort($modulesData, function($a, $b) {
            $result = 0;
            
            if (isset($a['errors'])) {
                $result = 1;
            } elseif (isset($b['errors'])) {
                $result = -1;
            }
            
            return $result;
        });
        
        $data = array_filter(
            array_merge(
                [
                    'Core'  => $coreData,
                ],
                $modulesData
            )
        );

        foreach ($data as $dataItemKey => $dataItem) {
            if (isset($dataItem['errors'])) {
                foreach ($dataItem['errors'] as $key => $error) {
                    $data[$dataItemKey]['errors'][$key] = $this->getHumanError($error);
                }
            }
        }
        
        return $data;
    }

    /**
     * @param string $error
     */
    protected function getHumanError($error)
    {
        $errorsList = static::getHumanErrors();

        return isset($errorsList[strval($error)])
            ? $errorsList[strval($error)]
            : 'Error';
    }

    /**
     * @return array
     */
    protected static function getHumanErrors()
    {
        return [
            '1013' => static::t('Unknown core version'),
            '9010' => static::t('The core may be checked only if a valid license key is present.'),
            '1022' => static::t('Cannot check the files for this module'),
            '1025' => static::t('The module may be checked only if a valid license key is present.'),
            '1027' => static::t('The module may be checked only if a valid license key is present.'),
        ];
    }
}