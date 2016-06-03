<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * @param array       $tree
     * @param string|null $prefix
     *
     * @return array
     */
    protected static function collapseDependencyTree($tree, $prefix = null)
    {
        $result = [];

        foreach ($tree as $node => $subTree) {
            $isSubTree = is_array($subTree)
                && array_filter(
                    array_map(
                        function ($item) {
                            return !is_int($item);
                        },
                        array_keys($subTree)
                    )
                );

            $subPrefix = ($prefix ? ($prefix . '.') : '') . $node;

            if ($isSubTree) {
                foreach (static::collapseDependencyTree($subTree, $subPrefix) as $key => $value) {
                    $result[$key] = $value;
                }
            } else {
                $result[$subPrefix] = $subTree;
            }
        }

        return $result;
    }

    /**
     * @param array  $dependency Dependency rules
     * @param string $formName   Form name
     *
     * @return array
     */
    protected static function prepareDependency($dependency, $model, $formName)
    {
        $currentPath = preg_replace('/^[^\.]*.|\.[^\.]*$/', '', $model);
        $prepared = [];
        foreach ($dependency as $key => $rule) {
            if ('..' === $key) {
                $key = $currentPath;
            }

            $prepared[$key] = $rule;
        }

        $condition = [];
        foreach (static::collapseDependencyTree($prepared, $formName) as $path => $value) {
            if (is_array($value)) {
                $value = array_map('strval', array_values($value));
                $condition[] = '(' . json_encode($value) . '.indexOf(' . $path . ') != -1) ';

            } elseif (is_scalar($value)) {
                $condition[] = $path . '==\'' . $value . '\'';
            }
        }

        return implode('&&', $condition);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // @todo: check and remove
        // parent::buildView($view, $form, $options);

        // @todo: $v.. move to separate method
        $vModel = str_replace(['[]', '[', ']'], ['', '.', ''], $view->vars['full_name']);
        $vValidatePath = explode('.', $vModel);
        $validationRoot = array_shift($vValidatePath);
        $vValidatePath = implode('_', array_map(['XLite\Core\Converter', 'convertToCamelCase'], $vValidatePath));

        $vValidator = '$' . $validationRoot . '.' . strtolower($vValidatePath);
        $vValidators = [];
        foreach ($options['v_validate'] as $constraint => $rule) {
            $vValidators[] = $vValidator . '.' . $constraint;
            $options['v_validate'][$constraint]['rule']['model'] = $vModel;
        }

        $rowClasses = implode(' ', array_map(function ($item) {
            return str_replace('_', '-', $item) . '-row';
        }, array_slice($view->vars['block_prefixes'], 0, -1)));

        $rowClasses .= ' ' . str_replace('_', '-', $view->vars['id']);

        $view->vars = array_replace($view->vars, [
            'show_label_block'  => $options['show_label_block'] === null || $options['show_label_block'],
            'label_description' => $options['label_description'],
            'description'       => $options['description'],
            'help'              => $options['help'],

            'input_grid' => $options['input_grid'],

            'form_row_class' => $options['form_row_class'] . ' ' . $rowClasses,

            'v_model'         => $vModel,
            'v_validate_path' => $vValidatePath,
            'v_validate'      => $options['v_validate'] ? json_encode($options['v_validate']) : '',
            'v_validators'    => $vValidators,
            'v_validator'     => $vValidator,

            'data_object' => $options['data_object'],
            'view_object' => $options['view_object'],

            'v_show' => $options['show_when']
                ? static::prepareDependency($options['show_when'], $vModel, $validationRoot)
                : '',
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'show_label_block'  => null,
            'label_description' => '',
            'description'       => '',
            'help'              => '',

            'input_grid' => '',

            'form_row_class' => 'form-group',

            'v_validate' => [],

            'data_object' => null,
            'view_object' => null,

            'show_when' => [],
        ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FormType';
    }
}
