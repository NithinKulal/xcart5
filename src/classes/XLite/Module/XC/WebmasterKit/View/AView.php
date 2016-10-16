<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\View;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Profiler data
     *
     * @var array
     */
    protected static $profilerInfo;

    /**
     * So called "static constructor".
     * NOTE: do not call the "parent::__constructStatic()" explicitly: it will be called automatically
     *
     * @return void
     */
    public static function __constructStatic()
    {
        static::$profilerInfo = array(
            'markTemplates' => \XLite\Module\XC\WebmasterKit\Core\Profiler::markTemplatesEnabled(),
            'countDeep'     => 0,
            'countLevel'    => 0,
        );
    }

    /**
     * Prepare template display
     *
     * @param string $template Template short path
     *
     * @return array
     */
    protected function prepareTemplateDisplay($template)
    {
        $result = parent::prepareTemplateDisplay($template);
        $cnt = static::$profilerInfo['countDeep']++;
        $cntLevel = static::$profilerInfo['countLevel']++;

        if (static::$profilerInfo['markTemplates']) {
            $template = substr($template, strlen(LC_DIR_SKINS));
            $markTplText = get_class($this) . ' : ' . $template . ' (' . $cnt . ')'
                . ($this->viewListName ? ' [\'' . $this->viewListName . '\' list child]' : '');

            echo ('<!-- ' . $markTplText . ' {@! -->');
            $result['markTplText'] = $markTplText;
        }

        return $result;
    }

    /**
     * Finalize template display
     *
     * @param string $template     Template short path
     * @param array  $profilerData Profiler data which is calculated and returned in the 'prepareTemplateDisplay' method
     *
     * @return void
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        if (1 < func_num_args()) {
            $profilerData = func_get_arg(1);

        } else {
            $profilerData = static::$profilerInfo;
        }

        if (isset($profilerData['markTplText'])) {
            echo ('<!-- !@} ' . $profilerData['markTplText'] . ' -->');
        }

        if (isset($profilerData['timePoint'])) {
            \XLite\Module\XC\WebmasterKit\Core\Profiler::getInstance()->log($profilerData['timePoint']);
        }

        static::$profilerInfo['countLevel']--;

        parent::finalizeTemplateDisplay($template, $profilerData);
    }

    // {{{ Helpers

    /**
     * Get current widget class name
     *
     * @return string
     */
    protected function getCurrentClassName()
    {
        return get_called_class();
    }

    // }}}
}

// Call static constructor
\XLite\Module\XC\WebmasterKit\View\AView::__constructStatic();
