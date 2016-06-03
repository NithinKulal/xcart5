<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

/**
 * Nested set data processor
 */
class NestedSet extends \XLite\Logic\ALogic
{
    protected $inputData    = array();

    /**
     * Input data should have the following format
     *
     * array (
     *     array (
     *       'id' => '1',
     *       'parent_id' => NULL,
     *       'lpos' => '1',
     *       'rpos' => '12',
     *       'depth' => '-1',
     *       'pos' => '0',
     *       'enabled' => '1',
     *       'subnodes_count_all' => '2',
     *       'subnodes_count_enabled' => '2',
     *     ),
     * )
     *
     * @param array $inputData Input data
     */
    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }

    /**
     * Correct structure: lpos, rpos and depth fields
     *
     * @return array
     */
    public function recalculateStructure()
    {
        if (empty($this->inputData)) {
            return $this->getDefaultResult();
        }

        $flattenedSorted = $this->getSortedByParentAndFlat();

        $rootIndex = 0;
        $flattenedSorted[$rootIndex]['lpos'] = 1;
        $flattenedSorted[$rootIndex]['rpos'] = 2;
        $flattenedSorted[$rootIndex]['depth'] = -1;

        $this->correctNodesData($flattenedSorted);

        $nodes = array();
        foreach ($flattenedSorted as $node) {
            $nodes[$node['id']] = $node;
        }

        return $this->collectUpdateData($nodes);
    }

    /**
     * Initiate nodes, sort them by parent id and flatten afterwards
     *
     * @return array
     */
    protected function getSortedByParentAndFlat()
    {
        $sortedByParent = array();

        foreach ($this->inputData as $node) {
            $sortedByParent[intval($node['parent_id'])][] = array(
                'id'                => $node['id'],
                'parent_id'         => $node['parent_id'],
                'lpos'              => 0,
                'rpos'              => 0,
                'depth'             => 0,
                'pos'               => $node['pos'],
                'enabled'           => $node['enabled'],
                'subnodes'           => 0,
                'subnodes_enabled'   => 0,
                'total_subnodes'     => 0,
            );
        }

        ksort($sortedByParent);

        $flattenedSorted = array();
        foreach ($sortedByParent as $children) {
            foreach ($children as $c) {
                $flattenedSorted[] = $c;
            }
        }

        $sortedByParent = array();
        unset($sortedByParent);

        return $flattenedSorted;
    }

    /**
     * Default value for recalculateStructure
     *
     * @return array
     */
    protected function getDefaultResult()
    {
        return array(
            array(),
            array(),
        );
    }

    /**
     * Collect data for update. Only changed values
     *
     * @param array     $nodes      Nodes
     *
     * @return array
     */
    protected function collectUpdateData(array $nodes)
    {
        $updateData = array();
        $updateQuickFlagsData = array();

        foreach ($this->inputData as $node) {
            $catId = intval($node['id']);

            if ($this->isNestedSetDataChanged($node, $nodes[$catId])) {
                $updateData[$catId] = array(
                    'lpos'  => $nodes[$catId]['lpos'],
                    'rpos'  => $nodes[$catId]['rpos'],
                    'depth' => $nodes[$catId]['depth'],
                );
            }

            if ($this->isQuickDataDataChanged($node, $nodes[$catId])) {
                $updateQuickFlagsData[$catId] = array(
                    'quick_data_assoc_id'    => $catId,
                    'subnodes_count_all'     => $nodes[$catId]['subnodes'],
                    'subnodes_count_enabled' => $nodes[$catId]['subnodes_enabled'],
                );
            }
        }

        return array($updateData, $updateQuickFlagsData);
    }

    /**
     * Check if nestedSet data changed
     *
     * @param array     $node       Current node
     * @param array     $originalNode  Original node
     *
     * @return boolean
     */
    protected function isNestedSetDataChanged($node, $originalNode)
    {
        return intval($node['lpos'])    !== intval($originalNode['lpos'])
            || intval($node['rpos'])    !== intval($originalNode['rpos'])
            || intval($node['depth'])   !== intval($originalNode['depth']);
    }

    /**
     * Check if quickData changed
     *
     * @param array     $node       Current node
     * @param array     $originalNode  Original node
     *
     * @return boolean
     */
    protected function isQuickDataDataChanged($node, $originalNode)
    {
        return is_null($node['subnodes_count_all'])
            || is_null($node['subnodes_count_enabled'])
            || intval($node['subnodes_count_all'])     !== intval($originalNode['subnodes'])
            || intval($node['subnodes_count_enabled']) !== intval($originalNode['subnodes_enabled']);
    }

    /**
     * Recursively calculate nodes lpos, rpos and depth
     *
     * @param array   &$nodes       Nodes data array
     * @param integer $currentId    Current node array index OPTIONAL
     */
    protected function correctNodesData(&$nodes, $currentId = 0)
    {
        $current = $nodes[$currentId];

        $idx = $current['lpos'] + 1;

        foreach ($nodes as $i => $node) {
            if ($node['parent_id'] != $current['id']) {
                continue;
            }
            // {{{ Calculate nestedSet data
            $nodes[$i]['depth']    = $current['depth'] + 1;
            $nodes[$i]['lpos']     = $idx;
            $nodes[$i]['rpos']     = $idx + 1;

            $this->correctNodesData($nodes, $i);

            $nodes[$currentId]['rpos'] = $nodes[$i]['rpos'] + 1;
            // }}}

            // {{{ Calculate quick data
            $nodes[$currentId]['subnodes']++;

            if ($node['enabled']) {
                $nodes[$currentId]['subnodes_enabled']++;
            }

            $nodes[$currentId]['total_subnodes'] += (1 + $nodes[$i]['total_subnodes']);

            // }}}
            $idx += (2 + 2 * $nodes[$i]['total_subnodes']);
        }
    }
}
