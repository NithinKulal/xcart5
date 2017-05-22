<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\View\Model;

/**
 * Settings dialog model widget
 */
abstract class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Amazon S3 required settings 
     * 
     * @var array
     */
    protected $amazonS3RequiredSettings = array(
        'access_key',
        'secret_key',
        'bucket',
    );

    /**
     * Check if field is valid and (if needed) set an error message
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        if (
            'default' == $section
            && \XLite::getController() instanceOf \XLite\Controller\Admin\Module
            && 'CDev\AmazonS3Images' == $this->getModule()->getActualName()
            && !$this->errorMessages
        ) {
            $vars = array();
            foreach ($data[self::SECTION_PARAM_FIELDS] as $field) {
                $vars[$field->getName()] = $field->getValue();
            }
            $client = \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance();
            if (
                !empty($vars['access_key'])
                && !empty($vars['secret_key'])
                && !empty($vars['bucket'])
                && !$client->checkSettings($vars['bucket'], $vars['access_key'], $vars['secret_key'])
            ) {
                $this->addErrorMessage(
                    'access_key',
                    'Connection to Amazon S3 failed.'
                    . ' Check whether the AWS Access key, AWS Secret key and bucket name specified in the module settings are correct.',
                    $data
                );
            }
        }
    }

    /**
     * Check - option is required or not
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return boolean
     */
    protected function isOptionRequired(\XLite\Model\Config $option)
    {
        return parent::isOptionRequired($option)
            || ('CDev\AmazonS3Images' == $option->getCategory() && in_array($option->getName(), $this->amazonS3RequiredSettings));
    }

}
