<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Includes\Utils\Converter;
use XLite\Core\Database;
use XLite\Core\Doctrine\ORM\Mapping\MetadataLoaderInterface;
use XLite\Core\MagicMethodsIntrospectionInterface;

/**
 * Translation-owner abstract class
 *
 * @MappedSuperclass
 */
abstract class I18n extends \XLite\Model\AEntity implements MetadataLoaderInterface, MagicMethodsIntrospectionInterface
{
    /**
     * Current entity language
     *
     * @var string
     */
    protected $editLanguage;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $translations;

    /** @var array Translatable fields grouped by type */
    protected static $i18nFieldNamesByType = [];

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set current entity language
     *
     * @param string $code Code to set
     *
     * @return self
     */
    public function setEditLanguage($code)
    {
        $this->editLanguage = $code;

        return $this;
    }

    /**
     * Return all translations
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add translation to the list
     *
     * @param \XLite\Model\Base\Translation $translation Translation to add
     *
     * @return void
     */
    public function addTranslations(\XLite\Model\Base\Translation $translation)
    {
        $this->translations[] = $translation;
    }

    /**
     * Get translation
     *
     * @param string  $code             Language code OPTIONAL
     * @param boolean $allowEmptyResult Flag OPTIONAL
     *
     * @return \XLite\Model\Base\Translation
     */
    public function getTranslation($code = null, $allowEmptyResult = false)
    {
        $result = $this->getHardTranslation($code);

        if (!isset($result) && !$allowEmptyResult) {
            $class  = $this instanceof \Doctrine\ORM\Proxy\Proxy ? get_parent_class($this) : get_class($this);
            $class .= 'Translation';

            $result = new $class();
            $result->setOwner($this);
            $result->setCode($this->getTranslationCode($code));
        }

        return $result;
    }

    /**
     * Search for translation
     *
     * @param string $code Language code OPTIONAL
     *
     * @return \XLite\Model\Base\Translation
     */
    public function getHardTranslation($code = null)
    {
        return \Includes\Utils\ArrayManager::searchInObjectsArray(
            $this->getTranslations()->toArray(),
            'getCode',
            $this->getTranslationCode($code)
        );
    }

    /**
     * Get translation in safe mode
     *
     * @param string $code Language code OPTIONAL
     *
     * @return \XLite\Model\Base\Translation
     */
    public function getSoftTranslation($code = null)
    {
        $result = null;

        // Select by languages query (current languge -> default language -> hardcoded default language)
        $query = \XLite\Core\Translation::getLanguageQuery($this->getTranslationCode($code));
        foreach ($query as $code) {
            $result = $this->getTranslation($code, true);
            if (isset($result)) {
                break;
            }
        }

        // Get first translation
        if (!isset($result)) {
            $result = $this->getTranslations()->first() ?: null;
        }

        // Get empty dump translation with specified code
        if (!isset($result)) {
            $result = $this->getTranslation(array_shift($query));
        }

        return $result;
    }

    /**
     * Check for translation
     *
     * @param string $code Language code OPTIONAL
     *
     * @return boolean
     */
    public function hasTranslation($code = null)
    {
        return (bool) $this->getHardTranslation($code);
    }

    /**
     * Get translation codes
     *
     * @return array
     */
    public function getTranslationCodes()
    {
        return \Includes\Utils\ArrayManager::getObjectsArrayFieldValues($this->getTranslations()->toArray(), 'getCode');
    }

    /**
     * Detach self
     *
     * @return void
     */
    public function detach()
    {
        parent::detach();

        foreach ($this->getTranslations() as $translation) {
            $translation->detach();
        }
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $entity = parent::cloneEntity();

        foreach ($entity->getSoftTranslation()->getRepository()->findBy(array('owner' => $entity)) as $translation) {
            $newTranslation = $translation->cloneEntity();
            $newTranslation->setOwner($entity);
            $entity->addTranslations($newTranslation);
            Database::getEM()->persist($newTranslation);
        }

        return $entity;
    }

    /**
     * Return current translation code
     *
     * @param string $code Language code OPTIONAL
     *
     * @return string
     */
    protected function getTranslationCode($code = null)
    {
        if (!isset($code)) {
            if ($this->editLanguage) {
                $code = $this->editLanguage;

            } elseif (\XLite\Logic\Export\Generator::getLanguageCode()) {
                $code = \XLite\Logic\Export\Generator::getLanguageCode();

            } elseif (\XLite\Logic\Import\Importer::getLanguageCode()) {
                $code = \XLite\Logic\Import\Importer::getLanguageCode();

            } elseif (\XLite\Core\Translation::getTmpMailTranslationCode()) {
                $code = \XLite\Core\Translation::getTmpMailTranslationCode();

            } else {
                $code = $this->getSessionLanguageCode();
            }
        }

        return $code;
    }

    /**
     * Get default language code
     *
     * @return string
     */
    protected function getSessionLanguageCode()
    {
        $lng = \XLite\Core\Session::getInstance()->getLanguage();
        return $lng ? $lng->getCode() : 'en';
    }

    public function __call($method, array $arguments = array())
    {
        return $this->hasMagicMethod($method)
            ? $this->callI18nMethod($method, $arguments)
            : parent::__call($method, $arguments);

    }

    public function hasMagicMethod($method)
    {
        $getter = stripos($method, 'get') === 0;
        $setter = stripos($method, 'set') === 0;

        if ($getter || $setter) {
            $fieldName = strtolower(Converter::convertToCamelCase(substr($method, 3)));

            return in_array($fieldName, $this->getI18nFieldNames());
        }

        return false;
    }

    protected function callI18nMethod($method, $arguments)
    {
        $getter = stripos($method, 'get') === 0;

        if ($getter) {
            return $this->getSoftTranslation()->$method();
        }

        $translation = $this->getTranslation();

        if (!$this->hasTranslation($translation->getCode())) {
            $this->addTranslations($translation);
        }

        return $translation->$method($arguments[0]);
    }

    protected function getI18nFieldNames()
    {
        $type = get_class($this);

        if (!isset(static::$i18nFieldNamesByType[$type])) {
            $translationClass = $type . 'Translation';
            $metadata         = Database::getEM()->getClassMetadata($translationClass);

            static::$i18nFieldNamesByType[$type] = array_diff(
                array_map(
                    'strtolower',
                    array_map('Includes\Utils\Converter::convertToCamelCase', $metadata->fieldNames)
                ),
                Translation::getInternalProperties()
            );
        }

        return static::$i18nFieldNamesByType[$type];
    }

    public static function loadMetadata(ClassMetadata $metadata, ClassMetadataFactory $metadataFactory)
    {
        $className = get_called_class();

        $isEntity = !$metadata->isMappedSuperclass;

        if ($isEntity && !isset($metadata->associationMappings['translations'])) {
            $translationClassName = $className . 'Translation';

            (new ClassMetadataBuilder($metadata))
                ->createOneToMany('translations', $translationClassName)
                ->mappedBy('owner')
                ->cascadeAll()
                ->build();
        }
    }
}
