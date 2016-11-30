<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * ThemeTweaker controller
 */
class InlineEditable extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isAJAX();
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'update_field';
        $list[] = 'save_image';

        return $list;
    }

    /**
     * Gets changeset of updated fields and applies it to data storage.
     * Returns HTTP 500 error in case of errors.
     */
    protected function doActionUpdateField()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);

        // To catch all possible fatal errors
        ob_start();

        $errors = array();

        $nonFiltered = \XLite\Core\Request::getInstance()->getNonFilteredData();

        // update images
        $images = isset($nonFiltered['images']) ? $nonFiltered['images'] : array();
        $updates = isset($nonFiltered['changeset']) ? $nonFiltered['changeset'] : array();
        $processedImages = array();

        foreach ($images as $imageId) {
            $temporaryFile = $imageId
                ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($imageId)
                : null;

            if ($temporaryFile) {
                $tempUrl = $temporaryFile->getFrontURL();
                $file = $this->createContentImageEntity($temporaryFile);

                if ($file) {
                    $newUrl = str_replace(\XLite\Core\URLManager::getShopURL(), '', $file->getFrontURL());
                    $processedImages[$imageId] = $newUrl;

                    $updates = array_map(function($record) use ($tempUrl, $newUrl) {
                        $record['value'] = str_replace($tempUrl, $newUrl, $record['value']);
                        return $record;
                    }, $updates);
                } else {
                    $errors[] = static::t('Error on saving image');
                }
            } else {
                $errors[] = static::t('Unknown temporary file');
            }
        }

        foreach ($updates as $data) {
            $result = $this->applyUpdate($data);
            if ($result) {
                $errors[] = $result;
            }
        }

        \XLite\Core\Database::getEM()->flush();

        ob_end_clean();

        if ($errors) {
            $this->headerStatus(500);
            $response = array(
                'message' => $this->buildErrorMessage($errors),
            );
        } else {
            $response = array(
                'message' => static::t('Entity was successfully saved'),
                'imageUrls' => $processedImages
            );
        }

        $this->displayJSON($response);
    }

    /**
     * Tries to apply given field record to database.
     * Valid record should be in such format:
     * array {
     *     'model' => <ENTITY_CLASS>,
     *     'identifier' => <IDENTIFIER_VALUE>,
     *     'property' => <PROPERTY_NAME>,
     *     'value' => <HTML_STRING>
     * }
     *
     * @param  array $data Single field change record
     */
    protected function applyUpdate($data)
    {
        $message = null;
        if ($this->validateData($data)) {
            $primaryKey = \XLite\Core\Database::getRepo($data['model'])->getPrimaryKeyField();
            $entity = \XLite\Core\Database::getRepo($data['model'])->findOneBy(array($primaryKey => $data['identifier']));

            if ($entity && $entity->isEditableProperty($data['property'])) {
                if ($entity instanceof \XLite\Model\Base\I18n) {
                    $this->enableTranslation($entity);
                }
                $setter = 'set' . ucfirst($data['property']);
                $value = $this->purifyValue($data['value']);
                $entity->$setter($value);
            } else {
                $message = 'Unknown entity or property is not writable';
            }
        } else {
            $message = 'Invalid parameters';
        }

        return $message;
    }


    /**
     * Enables appropriate edit language for i18n entity.
     * Checks for translation in customer zone language, creates it if it's not present and enables customer zone language for this edit.
     *
     * @param  \XLite\Model\Base\I18n $entity Translatable entity
     */
    protected function enableTranslation($entity)
    {
        // Get customer zone language because update comes from customer interface
        $cartLang = \XLite\Core\Session::getInstance()->getCurrentLanguage('customer');

        // Check and possibly create translation to ensure entity won't throw any errors on other empty fields.
        if (!$entity->hasTranslation($cartLang)) {
            $newTranslation = $entity->getSoftTranslation()->cloneEntity();
            $newTranslation->setCode($cartLang);
            $newTranslation->setOwner($entity);
            $entity->addTranslations($newTranslation);
            \XLite\Core\Database::getEM()->persist($newTranslation);
        }

        $entity->setEditLanguage($cartLang);
    }

    /**
     * Validates field change record.
     * Checks for fields presence
     *
     * @param  array $data Field change record
     * @return boolean
     */
    protected function validateData($data)
    {
        return $data['model']
            && $data['identifier']
            && $data['property']
            && isset($data['value']);
    }

    /**
     * Builds translated error message for response
     * @param  array  $errors Array of error messages
     * @return string
     */
    protected function buildErrorMessage(array $errors)
    {
        $delimiter = '<br> - ';
        $errorString = implode($delimiter, $errors);
        return static::t('Unable to save edited content. Errors: ') . $delimiter . static::t($errorString);
    }

    /**
     * Cleans given value from unwanted tags and code.
     * Using HTMLPurifier
     *
     * @param  string $dirty Value to be cleaned
     * @return string
     */
    protected function purifyValue($dirty)
    {
        return \XLite\Core\HTMLPurifier::purify($dirty);
    }

    /**
     * Accepts id of temporary image and moves it to Image\Content repository.
     * Returns HTTP 500 in case of error
     */
    protected function doActionSaveImage()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);

        $error = '';

        $temporaryId = \XLite\Core\Request::getInstance()->id;

        $temporaryFile = $temporaryId
            ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($temporaryId)
            : null;

        if ($temporaryFile) {
            $file = $this->createContentImageEntity($temporaryFile);

            if ($file) {
                $response = array(
                    'size'    => $file->getSize(),
                    'width'   => $file->getWidth(),
                    'height'  => $file->getHeight(),
                    'url'     => $file->getFrontURL(),
                    'id'      => $file->getId(),
                );
            } else {
                $error = static::t('Error on saving image');
            }
        } else {
            $error = static::t('Unknown temporary file');
        }

        if ($error) {
            $this->headerStatus(500);
            $response = array(
                'message' => $error,
            );
        }

        $this->displayJSON($response);
    }

    /**
     * Creates and persists XLite\Model\Image\Content object from given temporary file.
     *
     * @param  \XLite\Model\TemporaryFile $temporaryFile Temporary file with image
     * @return \XLite\Model\Image\Content
     */
    protected function createContentImageEntity($temporaryFile)
    {
        $file = new \XLite\Model\Image\Content();

        if ($temporaryFile->isURL()) {
            $file->loadFromURL($temporaryFile->getPath(), false);

        } else {
            $file->loadFromLocalFile(
                $temporaryFile->getStoragePath(),
                pathinfo($temporaryFile->getPath(), \PATHINFO_FILENAME)
                . '.' . pathinfo($temporaryFile->getPath(), \PATHINFO_EXTENSION)
            );
        }

        \XLite\Core\Database::getEM()->persist($file);
        \XLite\Core\Database::getEM()->flush();

        return $file;
    }
}
