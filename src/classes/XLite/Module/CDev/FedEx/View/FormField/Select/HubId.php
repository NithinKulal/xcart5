<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\FormField\Select;

/**
 * HUB ID selector for settings page
 */
class HubId extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '5185' => 'ALPA Allentown',
            '5303' => 'ATGA Atlanta',
            '5281' => 'CHNC Charlotte',
            '5929' => 'COCA Chino',
            '5751' => 'DLTX Dallas',
            '5802' => 'DNCO Denver',
            '5481' => 'DTMI Detroit',
            '5087' => 'EDNJ Edison',
            '5431' => 'GCOH Grove City',
            '5436' => 'GPOH Groveport Ohio',
            '5771' => 'HOTX Houston',
            '5465' => 'ININ Indianapolis',
            '5648' => 'KCKS Kansas City',
            '5902' => 'LACA Los Angeles',
            '5254' => 'MAWV Martinsburg',
            '5379' => 'METN Memphis',
            '5552' => 'MPMN Minneapolis',
            '5531' => 'NBWI New Berlin',
            '5110' => 'NENY Newburgh',
            '5015' => 'NOMA Northborough',
            '5327' => 'ORFL Orlando',
            '5194' => 'PHPA Philadelphia',
            '5854' => 'PHAZ Phoenix',
            '5150' => 'PTPA Pittsburgh',
            '5893' => 'RENV Reno',
            '5958' => 'SACA Sacramento',
            '5843' => 'SCUT Salt Lake City',
            '5983' => 'SEWA Seattle',
            '5631' => 'STMO St. Louis',
        );
    }
}