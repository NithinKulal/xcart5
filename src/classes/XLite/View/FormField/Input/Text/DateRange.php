<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Date range
 */
class DateRange extends \XLite\View\FormField\Input\Text
{
    /**
     * Labels displayed
     *
     * @var   boolean
     */
    protected static $labelsDisplayed = false;

    /**
     * Parse range as string
     *
     * @param string $string String
     * @param string $format Format
     *
     * @return array
     */
    public static function convertToArray($string, $format = null)
    {
        $result = array(0, 0);

        if (!empty($string) && is_string($string)) {

            $format = ($format ?: static::getDateFormat()) . ' H:i:s';

            $dates = explode(static::getDatesSeparator(), $string);

            if (!empty($dates[0])) {
                $startDate = \DateTime::createFromFormat($format, trim($dates[0]) . ' 0:00:00');
                if ($startDate) {
                    $result[0] = \XLite\Core\Converter::convertTimeToServer($startDate->getTimestamp());
                }
            }

            if (!empty($dates[1])) {
                $endDate = \DateTime::createFromFormat($format, trim($dates[1]) . ' 23:59:59');
                if ($endDate) {
                    $result[1] = \XLite\Core\Converter::convertTimeToServer($endDate->getTimestamp());
                }
            }
        }

        return $result;
    }

    /**
     * Get used  date format
     *
     * @param boolean $forJS Flag: return format for JS DateRangePicker script (true) or for php's date() function (false)
     *
     * @return string
     */
    protected static function getDateFormat($forJS = false)
    {
        return $forJS ? 'DD-MMM-YYYY' : 'd-M-Y';
    }

    /**
     * Get separate string between start date and end date
     *
     * @return string
     */
    protected static function getDatesSeparator()
    {
        return ' ~ ';
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/moment-with-langs.min.js';
        $list[static::RESOURCE_JS][] = array(
            'file'      => 'js/daterangepicker.js',
            'no_minify' => true,
        );
        $list[static::RESOURCE_CSS][] = 'css/daterangepicker.css';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/input/text/date_range.css';

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

        $list[] = $this->getDir() . '/js/date_range.js';

        return $list;
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            $value = $this->convertToString($value);
        }

        parent::setValue($value);
    }

    /**
     * Get formatted range
     *
     * @return string
     */
    protected function convertToString(array $value)
    {
        if (!empty($value[0]) || !empty($value[1])) {
            $format = static::getDateFormat();
            $value[0] = !empty($value[0]) ? date($format, $value[0]) : date($format);
            $value[1] = !empty($value[1]) ? date($format, $value[1]) : date($format);
            $value = implode(' ~ ', $value);

        } else {
            $value = '';
        }

        return $value;
    }

    /**
     * Add attribute 'data-end-date' to input field
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $result = parent::getCommonAttributes();

        $result['data-end-date'] = date(static::getDateFormat(), \XLite\Core\Converter::convertTimeToUser());
        $result['data-datarangeconfig'] = $this->getDateRangeConfig();

        return $result;
    }

    /**
     * Get config settings for DateRangePicker
     *
     * @return string
     */
    protected function getDateRangeConfig()
    {
        $lng = \XLite\Core\Session::getInstance()->getLanguage()
            ? \XLite\Core\Session::getInstance()->getLanguage()->getCode()
            : 'en';

        $config = array(
            'separator' => static::getDatesSeparator(),
            'language'  => $lng,
            'format'    => static::getDateFormat(true),
            'shortcuts' => array(),
            'customShortcuts' => array(
                array(
                    'name' => 'today',
                ),
                array(
                    'name' => 'this week',
                ),
                array(
                    'name' => 'this month',
                ),
                array(
                    'name' => 'this quarter',
                ),
                array(
                    'name' => 'this year',
                ),
            ),
        );

        return json_encode($config);
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $list = parent::assembleClasses($classes);

        $list[] = 'date-range';

        return $list;
    }

    /**
     * Some JavaScript code to insert
     *
     * @return string
     */
    protected function getInlineJSCode()
    {
        return parent::getInlineJSCode() . PHP_EOL
            . 'jQuery.dateRangePickerLanguages.en = ' . json_encode($this->getJavascriptLanguagesLabels()) . PHP_EOL;
    }

    /**
     * Get languages labels
     *
     * @return array
     */
    protected function getJavascriptLanguagesLabels()
    {
        return array(
            'selected'        => static::t('Selected:'),
            'days'            => static::t('Days'),
            'day'             => static::t('Day'),
            'apply'           => static::t('Close'),
            'week-1'          => static::t('MO'),
            'week-2'          => static::t('TU'),
            'week-3'          => static::t('WE'),
            'week-4'          => static::t('TH'),
            'week-5'          => static::t('FR'),
            'week-6'          => static::t('SA'),
            'week-7'          => static::t('SU'),
            'month-name'      => array(
                static::t('JANUARY'),
                static::t('FEBRUARY'),
                static::t('MARCH'),
                static::t('APRIL'),
                static::t('MAY'),
                static::t('JUNE'),
                static::t('JULY'),
                static::t('AUGUST'),
                static::t('SEPTEMBER'),
                static::t('OCTOBER'),
                static::t('NOVEMBER'),
                static::t('DECEMBER'),
            ),
            'shortcuts'       => static::t('Shortcuts'),
            'past'            => static::t('Past'),
            '7days'           => static::t('7days'),
            '14days'          => static::t('14days'),
            '30days'          => static::t('30days'),
            'previous'        => static::t('Previous'),
            'prev-week'       => static::t('Week'),
            'prev-month'      => static::t('Month'),
            'prev-quarter'    => static::t('Quarter'),
            'prev-year'       => static::t('Year'),
            'less-than'       => static::t('Date range should longer than %d days'),
            'more-than'       => static::t('Date range should less than %d days'),
            'default-more'    => static::t('Please select a date range longer than %d days'),
            'default-less'    => static::t('Please select a date range less than %d days'),
            'default-range'   => static::t('Please select a date range between %d and %d days'),
            'default-default' => static::t('Please select a date range'),
            'today'           => static::t('Today'),
            'this week'       => static::t('This week'),
            'this month'      => static::t('This month'),
            'this quarter'    => static::t('This quarter'),
            'this year'       => static::t('This year'),
        );
    }

    /**
     * Get default placeholder
     *
     * @return string
     */
    protected function getDefaultPlaceholder()
    {
        return static::t('Enter date range');
    }
}
