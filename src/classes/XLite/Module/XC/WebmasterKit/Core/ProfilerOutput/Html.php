<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core\ProfilerOutput;

class Html
{
    const DEC_POINT = '.';
    const THOUSANDS_SEP = ' ';

    protected $points;
    protected $messages;

    /**
     * @inheritDoc
     */
    public function __construct($points, $messages)
    {
        $this->points = $points;
        $this->messages = $messages;
    }

    /**
     * Display profiler report
     *
     * @return void
     */
    public function output()
    {
        echo ($this->getMessagesData());
        echo ($this->getPointsData());

        echo ('</div>');
    }
    
    protected function getMessagesData()
    {
        $html = '';

        if (!empty($this->messages)) {
            $html = <<<HTML
<br /><br />
<table cellspacing="0" cellpadding="3" border="1" style="width: auto; top: 0; z-index: 10000; background-color: #fff;">
    <caption style="font-weight: bold; text-align: left;">Profiler Messages</caption>
HTML;

            foreach ($this->messages as $message) {
                $html .= <<<HTML
<tr><td>$message</td></tr>
HTML;
            }

            $html .= <<<HTML
</table>
HTML;

        }

        return $html;
    }

    protected function getPointsData()
    {
        $html = '';

        if ($this->points) {
            $html = <<<HTML
<table cellspacing="0" cellpadding="3" border="1" style="width: auto;">
    <caption style="font-weight: bold; text-align: left;">Log points</caption>
    <tr>
        <th>Duration, sec.</th>
        <th>Point name</th>
    </tr>
HTML;

            foreach ($this->points as $name => $d) {
                $html .= '<tr><td>'
                    . number_format($d['time'], 4, static::DEC_POINT, static::THOUSANDS_SEP)
                    . '</td><td>'
                    . $name
                    . '</td></tr>';
            }

            $html .= '</table>';
        }
        
        return $html;
    }

}