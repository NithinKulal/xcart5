<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Base;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class TestDTO
 */
abstract class ADTO extends CommonCell
{
    /**
     * @param static                    $dto
     * @param ExecutionContextInterface $context
     */
    public static function validate($dto, ExecutionContextInterface $context)
    {
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string                    $field Field path in dot notation
     * @param string                    $message
     */
    protected static function addViolation($context, $field, $message)
    {
        $path = explode('.', $field);

        $context->buildViolation($message)
            ->atPath(
                array_shift($path)
                . implode('', array_map(
                    function ($item) {
                        return '[' . $item . ']';
                    },
                    $path
                ))
            )
            ->addViolation();
    }

    /**
     * @param ADTO  $dto
     * @param array $patch
     */
    protected static function compose($dto, $patch)
    {
        foreach ($patch as $sectionName => $section) {
            foreach ($section as $fieldName => $field) {
                if ($dto->offsetExists($sectionName)
                    && $dto->{$sectionName}->offsetExists($fieldName)
                ) {
                    if (!$dto->{$sectionName}->{$fieldName} instanceof CommonCell) {
                        $dto->{$sectionName}->{$fieldName}
                            = new CommonCell([$fieldName => $dto->{$sectionName}->{$fieldName}]);
                    }

                    $dto->{$sectionName}->{$fieldName}->setData($field);
                }
            }
        }
    }

    /**
     * @param ADTO   $dto
     * @param string $sectionName
     * @param string $fieldName
     * @param string $subFieldName
     *
     * @return mixed
     */
    protected static function deCompose($dto, $sectionName, $fieldName, $subFieldName)
    {
        $result = null;

        if ($dto->offsetExists($sectionName)
            && $dto->{$sectionName}->offsetExists($fieldName)
        ) {
            $field = $dto->{$sectionName}->{$fieldName};

            if ($field instanceof CommonCell
                && $field->offsetExists($subFieldName)
            ) {
                $result = $field->{$subFieldName};
                unset($field[$subFieldName]);

                if (count($field) === 1) {
                    $field = $field->rewind();
                }
            }

            if (!$field instanceof CommonCell) {
                $dto->{$sectionName}->{$fieldName} = $field;
            }
        }

        return $result;
    }

    /**
     * @param mixed      $object
     * @param array|null $rawData
     *
     * @return mixed
     */
    abstract public function populateTo($object, $rawData = null);

    /**
     * @param mixed $object
     */
    abstract protected function init($object);

    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if (is_array($data)) {
            parent::__construct($data);
        } else {
            $this->init($data);
        }
    }

    /**
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    public function toScheme()
    {
        $result = $this->toArray();

        array_walk_recursive($result, function (&$item) {
            $item = '';
        });

        return $result;
    }
}
