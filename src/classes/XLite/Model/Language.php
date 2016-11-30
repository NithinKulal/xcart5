<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Language
 *
 * @Entity
 * @Table (name="languages",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="code3", columns={"code3"}),
 *          @UniqueConstraint (name="code2", columns={"code"})
 *      },
 *      indexes={
 *          @Index (name="added", columns={"added"}),
 *          @Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class Language extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer", unique=true)
     */
    protected $lng_id;

    /**
     * Language alpha-2 code (ISO 639-2)
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=2, unique=true)
     */
    protected $code;

    /**
     * Language alpha-3 code (ISO 639-3)
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=3, unique=true)
     */
    protected $code3 = '';

    /**
     * Right-to-left flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $r2l = false;

    /**
     * Language status (added/not added)
     *
     * @var integer
     *
     * @Column (type="boolean")
     */
    protected $added = false;

    /**
     * Language state (enabled/disabled)
     *
     * @var integer
     *
     * @Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Related module (Author\Name)
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $module;


    /**
     * Get module
     *
     * @return string
     */
    public function getValidModule()
    {
        $result = null;

        $module = $this->getModule();

        if (!empty($module) && preg_match('/\w+\\\\\w+/', $module)) {
            $class = \Includes\Utils\ModulesManager::getClassNameByModuleName($module);
            if (class_exists($class)) {
                $result = $module;
            }
        }

        return $result;
    }

    /**
     * Return true if current language is set as a default for customer interface
     *
     * @return boolean
     */
    public function getDefaultCustomer()
    {
        return $this->getCode() == \XLite\Core\Config::getInstance()->General->default_language;
    }

    /**
     * Return true if current language is set as a default for admin interface
     *
     * @return boolean
     */
    public function getDefaultAdmin()
    {
        return $this->getCode() == \XLite\Core\Config::getInstance()->General->default_admin_language;
    }

    /**
     * Return true if current language is set as a default for customer interface
     *
     * @return boolean
     */
    public function getDefaultAuth()
    {
        return (!\XLite\Core\Auth::getInstance()->isAdmin() && $this->getDefaultCustomer())
        || (\XLite\Core\Auth::getInstance()->isAdmin() && $this->getDefaultAdmin());
    }

    /**
     * Get flag URL
     *
     * @return string|void
     */
    public function getFlagURL()
    {
        $path = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'images/flags_svg/' . $this->getCode() . '.svg',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite\Core\Layout::PATH_COMMON
        );

        if (!$path) {
            $path = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                'images/flags_svg/__.svg',
                \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                \XLite\Core\Layout::PATH_COMMON
            );
        }

        return $path;
    }

    /**
     * Get flag URL
     *
     * @return string|void
     */
    public function getFlagFile()
    {
        $file = 'images/flags_svg/' . $this->getCode() . '.svg';
        $path = \XLite\Core\Layout::getInstance()->getResourceFullPath(
            $file,
            \XLite\Core\Layout::PATH_COMMON
        );

        return $path
            ? $file
            : 'images/flags_svg/__.svg';
    }

    /**
     * Remove all label translations to the language
     * Return true on success
     *
     * @return boolean
     */
    public function removeTranslations()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->deleteTranslations($this->getCode());
    }

    /**
     * Get default language code
     *
     * @return string
     */
    protected function getSessionLanguageCode()
    {
        return $this->getCode();
    }

    /**
     * Get lng_id
     *
     * @return integer 
     */
    public function getLngId()
    {
        return $this->lng_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Language
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code3
     *
     * @param string $code3
     * @return Language
     */
    public function setCode3($code3)
    {
        $this->code3 = $code3;
        return $this;
    }

    /**
     * Get code3
     *
     * @return string 
     */
    public function getCode3()
    {
        return $this->code3;
    }

    /**
     * Set r2l
     *
     * @param boolean $r2l
     * @return Language
     */
    public function setR2l($r2l)
    {
        $this->r2l = $r2l;
        return $this;
    }

    /**
     * Get r2l
     *
     * @return boolean 
     */
    public function getR2l()
    {
        return $this->r2l;
    }

    /**
     * Set added
     *
     * @param boolean $added
     * @return Language
     */
    public function setAdded($added)
    {
        $this->added = $added;
        return $this;
    }

    /**
     * Get added
     *
     * @return boolean 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Language
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set module
     *
     * @param string $module
     * @return Language
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Get module
     *
     * @return string 
     */
    public function getModule()
    {
        return $this->module;
    }
}
