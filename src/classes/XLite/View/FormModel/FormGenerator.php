<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class FormGenerator
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $factory;

    /**
     * @var FormBuilderInterface
     */
    protected $builder;

    /**
     * @param $schema
     *
     * @return array
     */
    public static function generateSections($schema)
    {
        $result = [];
        foreach ($schema as $name => $definition) {
            $sectionOptions = static::generateSection($definition);

            if ($sectionOptions) {
                $result[$name] = [
                    'XLite\View\FormModel\Type\Base\FieldSetType',
                    $sectionOptions
                ];
            }
        }

        return $result;
    }

    /**
     * @param $schema
     *
     * @return array
     */
    public static function generateSection($schema)
    {
        $result = [];

        if (!empty($schema['fields'])) {
            $result['fields'] = static::generateFields($schema['fields']);

            /** @var array $section */
            $section = $schema['section'];
            if (isset($section['label'])) {
                $result['label'] = (string) $section['label'];
            }

            if (isset($section['help'])) {
                $result['help'] = (string) $section['help'];
            }

            if (isset($section['description'])) {
                $result['description'] = (string) $section['description'];
            }

            if (array_key_exists('collapse', $section)) {
                $result['collapse'] = (bool) $section['collapse'];
            }

            if (array_key_exists('expanded', $section)) {
                $result['expanded'] = (bool) $section['expanded'];
            }
        }

        return $result;
    }

    /**
     * @param $schema
     *
     * @return array
     */
    public static function generateFields($schema)
    {
        $result = [];

        foreach ($schema as $name => $field) {
            $type = $field['type'];

            /** Remove 'type' and 'position' from options */
            $options = array_diff_key($field, array_flip(['type', 'position']));

            $options['label'] = array_key_exists('label', $options)
                ? $options['label']
                : false;

            if (!array_key_exists('required', $options)) {
                $options['required'] = false;
            }

            if (array_key_exists('constraints', $options)) {
                $constraints = [];
                $validators = [];
                foreach ($options['constraints'] as $constraint => $constraintOptions) {
                    $validatorConstraint = static::getValidatorConstraintName($constraint);
                    $validators[$validatorConstraint] = [
                        'rule'    => $constraintOptions,
                        'message' => isset($constraintOptions['message']) ? $constraintOptions['message'] : 'NotValid'
                    ];

                    if (class_exists($constraint)) {
                        $constraints[] = new $constraint($constraintOptions);
                    }

                    if ($constraint = 'XLite\Core\Validator\Constraints\MaxLength'
                        && isset($constraintOptions['length'])
                    ) {
                        $options['attr'] = array_replace(
                            isset($options['attr']) ? $options['attr'] : [],
                            ['maxlength' => $constraintOptions['length']]
                        );
                    }
                }

                $options['constraints'] = $constraints;
                $options['v_validate'] = $validators;
            }

            if (empty($options['v_validate'])) {
                $options['v_validate'] = ['Backend' => []];
            }

            $result[$name] = [$type, $options];
        }

        return $result;
    }

    public function __construct()
    {
        $builder = Forms::createFormFactoryBuilder();

        $validator = Validation::createValidator();
        $builder->addExtension(new ValidatorExtension($validator));

        foreach ($this->getTypeExtensions() as $extension) {
            $builder->addTypeExtension(new $extension);
        }

        $this->factory = $builder->getFormFactory();
    }

    /**
     * @todo: configurable form name?
     *
     * @param array $schema
     * @param mixed $dataObject
     * @param mixed $viewObject
     * @param array $options
     *
     * @return Form
     */
    public function generate($schema, $dataObject = null, $viewObject = null, array $options = [])
    {
        $formName = 'form';

        $options['data_object'] = $dataObject ? json_encode($dataObject->toScheme()) : '';
        $options['view_object'] = $viewObject ? json_encode($viewObject->toScheme()) : '';

        $this->createBuilder($formName, $dataObject, $options);

        foreach (static::generateSections($schema) as $name => $section) {
            $this->builder->add($name, $section[0], $section[1]);
        }

        // // Don't use field set for one section
        // if (!array_key_exists(AFormModel::SECTION_DEFAULT, $schema) || count($schema) > 1) {
        //     foreach ($this->generateSections($schema) as $name => $section) {
        //         $this->builder->add($name, $section[0], $section[1]);
        //     }
        // } else {
        //     foreach ($this->generateFields($schema[AFormModel::SECTION_DEFAULT]['fields']) as $name => $field) {
        //         $this->builder->add($name, $field[0], $field[1]);
        //     }
        // }

        return $this->builder->getForm();
    }

    /**
     * @param string $name
     * @param mixed  $data
     * @param array  $options
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    protected function createBuilder($name = 'form', $data = null, array $options = [])
    {
        $this->builder = $this->factory->createNamedBuilder(
            $name,
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $data,
            $options
        );
    }

    /**
     * @param string $constraint
     *
     * @return string
     */
    protected static function getValidatorConstraintName($constraint)
    {
        $name = [];
        if (preg_match('/XLite\\\\Module\\\\(\w+\\\\\w+)/i', $constraint, $matches)) {
            $name = array_merge($name, explode('\\', $matches[1]));
        }

        if (preg_match('/Validator\\\\Constraints\\\\([\w+\\\\]+)/', $constraint, $matches)) {
            $name = array_merge($name, explode('\\', $matches[1]));
        }

        return implode('_', $name);
    }

    /**
     * @return array
     */
    protected function getTypeExtensions()
    {
        return [
            'XLite\View\FormModel\Type\Base\FormTypeExtension',
            'XLite\View\FormModel\Type\Base\CSRFTypeExtension',
            'XLite\View\FormModel\Type\Base\ReturnURLTypeExtension',
            'XLite\View\FormModel\Type\Base\RadioTypeExtension',
        ];
    }
}
