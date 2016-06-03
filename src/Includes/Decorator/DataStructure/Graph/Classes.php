<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\DataStructure\Graph;

/**
 * Classes
 *
 */
class Classes extends \Includes\DataStructure\Graph
{
    /**
     * Reflection object
     *
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * Flag for so called "low-level" nodes
     *
     * @var boolean
     */
    protected $isLowLevelNode;

    /**
     * Flag for so called "top-level" stub nodes
     *
     * @var boolean
     */
    protected $isTopLevelNode;

    /**
     * Entity mark 
     * 
     * @var boolean
     */
    protected $entityMark;

    /**
     * Flag to determine if node was changed (e.g. its key was modified)
     *
     * @var boolean
     */
    protected $isChanged = false;

    /**
     * Index 
     * 
     * @var array
     */
    protected $index = array();

    // {{{ Quick index

    /**
     * Get node by index 
     * 
     * @param string $class Class name (key)
     *  
     * @return \Includes\Decorator\DataStructure\Graph\Classes
     */
    public function getByIndex($class)
    {
        return isset($this->index[$class]) ? $this->index[$class] : null;
    }

    /**
     * Add to index 
     * 
     * @param \Includes\Decorator\DataStructure\Graph\Classes $node Class node
     *  
     * @return void
     */
    public function addToIndex(\Includes\Decorator\DataStructure\Graph\Classes $node)
    {
        $this->index[$node->getKey()] = $node;
    }

    /**
     * Find node by key
     *
     * @param string $key Key to search
     *
     * @return array
     */
    public function find($key)
    {
        return $this->index ? $this->getByIndex($key) : parent::find($key);
    }

    // }}}

    // {{{ Constructor and common getters

    /**
     * Add child node
     *
     * @param \Includes\DataStructure\Graph $node Node to add
     *
     * @return void
     */
    public function addChild(\Includes\DataStructure\Graph $node)
    {
        parent::addChild($node);

        $node->setParentClass($this->getClass());
    }

    /**
     * Alias
     *
     * @return string
     */
    public function getClass()
    {
        return $this->prepareClassName($this->getKey());
    }

    /**
     * Getter for the flag
     *
     * @return boolean
     */
    public function isLowLevelNode()
    {
        return $this->isLowLevelNode;
    }

    /**
     * Getter for the flag
     *
     * @return boolean
     */
    public function isTopLevelNode()
    {
        return $this->isTopLevelNode;
    }

    /**
     * Check - class is Doctrine entity or not
     * 
     * @return boolean
     */
    public function isEntity()
    {
        if (!isset($this->entityMark)) {
            $class = $this->getClass();
            $this->entityMark = preg_match('/^XLite(?:\\\\Module\\\\[A-Za-z0-9]+\\\\[A-Za-z0-9]+)?\\\\Model\\\\/Ss', $class)
                && !preg_match('/^XLite(?:\\\\Module\\\\[A-Za-z0-9]+\\\\[A-Za-z0-9]+)?\\\\Model\\\\(?:Repo|QueryBuilder)\\\\/Ss', $class)
                && is_subclass_of($class, 'XLite\Model\AEntity');
        }

        return $this->entityMark;
    }

    /**
     * Return name of parent class
     *
     * @return string
     */
    public function getParentClass()
    {
        return $this->getReflection()->parentClass;
    }

    /**
     * Set name of parent class
     *
     * @param string $class Class name to set
     *
     * @return void
     */
    public function setParentClass($class)
    {
        $this->getReflection()->parentClass = $class;
    }

    /**
     * Return list of class implementing interfaces
     *
     * @return array
     */
    public function getInterfaces()
    {
        return $this->getReflection()->interfaces;
    }

    /**
     * Check if class implements interface
     *
     * @param string $interface Name of interface to check
     *
     * @return boolean
     */
    public function isImplements($interface)
    {
        return in_array($this->prepareClassName($interface), $this->getInterfaces());
    }

    /**
     * Check if class decorates another one
     *
     * @return boolean
     */
    public function isDecorator()
    {
        return $this->isImplements('XLite\Base\IDecorator');
    }

    /**
     * Return name of the module where the class defined
     *
     * @return string
     */
    public function getModuleName()
    {
        return \Includes\Utils\ModulesManager::getModuleNameByClassName($this->getClass());
    }

    /**
     * Return top-level child
     *
     * @return \Includes\DataStructure\Graph
     */
    public function getTopLevelNode()
    {
        $result = $this;

        if ($this->isDecorator()) {
            $children = $this->getChildren();
            if ($children) {
                $result = reset($children)->getTopLevelNode();
            }
        }

        return $result;
    }

    // }}}

    // {{{ Methods to modify graph

    /**
     * Set node key
     *
     * @param string  $key     Key to set
     * @param boolean $setFlag Flag OPTIONAL
     *
     * @return void
     */
    public function setKey($key, $setFlag = false)
    {
        foreach ($this->getChildren() as $node) {
            $node->setParentClass($key);
        }

        $this->moveClassFile($key);

        parent::setKey($key);

        if ($setFlag) {
            $this->isChanged = true;
        }
    }

    /**
     * Mark node as "low-level"
     *
     * @return void
     */
    public function setLowLevelNodeFlag()
    {
        $this->isLowLevelNode = true;
    }

    /**
     * Mark node as "top-level"
     *
     * @return void
     */
    public function setTopLevelNodeFlag()
    {
        $this->isTopLevelNode = true;
    }

    // }}}

    // {{{ Methods to get paths and source code

    /**
     * Name of the origin class file
     *
     * @param string $class Class name OPTIONAL
     * @param string $dir   Dir to file OPTIONAL
     *
     * @return string
     */
    public function getFile($class = null, $dir = null)
    {
        $dir = $dir ?: \Includes\Decorator\ADecorator::getCacheClassesDir();

        return $dir . $this->getPath($class);
    }

    /**
     * Transform class name into the relative path
     *
     * @param string $class Class name OPTIONAL
     *
     * @return string
     */
    public function getPath($class = null)
    {
        return \Includes\Utils\Converter::getClassFile($class ?: $this->getClass());
    }

    /**
     * Prepare source code of the class
     *
     * @param \Includes\DataStructure\Graph $parent Parent node OPTIONAL
     *
     * @return string
     */
    public function getSource(\Includes\DataStructure\Graph $parent = null)
    {
        if ($this->isChanged || $this->isDecorator()) {
            $result = $this->getActualSource($parent);

        } elseif ($this->isTopLevelNode()) {
            $result = $this->getEmptySource($parent);

        } else {
            $result = $this->getRegularSource();
        }

        return $result;
    }

    /**
     * Return modified DOC block
     *
     * @param array   $lines   Lines to add
     * @param boolean $replace Flag OPTIONAL
     * @param boolean $asTags  Flag OPTIONAL
     *
     * @return string
     */
    public function addLinesToDocBlock(array $lines, $replace = false, $asTags = true)
    {
        $separator = PHP_EOL . ' * ';

        if ($asTags) {
            $separator .= '@';

            if (!$replace) {
                foreach ($lines as $index => $line) {
                    $line = preg_split('/\s+/Ss', $line);

                    if (false !== strpos($this->getReflection()->docComment, '@' . array_shift($line))) {
                        unset($lines[$index]);
                    }
                }
            }
        }

        $lines = array_unique($lines);

        $result = $lines ? $separator . implode($separator, $lines) : '';

        if ($replace || !$this->getReflection()->docComment) {
            $result = '/**' . $result . PHP_EOL . ' */';

        } else {
            $result = preg_replace('/(\s+\*+\/)$/Ss', $result . '$1', $this->getReflection()->docComment);
        }

        return $result;
    }

    /**
     * Return modified DOC block
     *
     * @param array   $lines  Lines to add
     * @param boolean $asTags Flag OPTIONAL
     *
     * @return string
     */
    public function removeLinesFromDocBlock(array $lines, $asTags = true)
    {
        $pattern = $asTags
            ? \Includes\Decorator\Utils\Operator::getTagPattern($lines)
            : '/^(\s*\*\s*)?(' . implode('|', $lines) . ').*$/Smi';

        return preg_replace($pattern, '', $this->getReflection()->docComment);
    }

    /**
     * Actualize and return source code for node
     *
     * @param \Includes\DataStructure\Graph $parent Parent node OPTIONAL
     *
     * @return string
     */
    protected function getActualSource(\Includes\DataStructure\Graph $parent = null)
    {
        return \Includes\Decorator\Utils\Tokenizer::getSourceCode(
            $this->getFile(),
            $this->getActualNamespace(),
            $this->getClassBaseName(),
            $this->getActualParentClassName($parent),
            $this->removeLinesFromDocBlock(array('ListChild', 'AddListChild', 'ClearListChildren')),
            ($this->isLowLevelNode() || $this->isDecorator()) ? 'abstract' : null
        );
    }

    /**
     * Return source code for "top-level" decorator node
     *
     * @param \Includes\DataStructure\Graph $parent Parent node OPTIONAL
     *
     * @return string
     */
    protected function getEmptySource(\Includes\DataStructure\Graph $parent = null)
    {
        return '<?php' . PHP_EOL . PHP_EOL
            . (($namespace = $this->getActualNamespace()) ? ('namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL) : '')
            . (($comment = $this->removeLinesFromDocBlock(array('HasLifecycleCallbacks'))) ? ($comment . PHP_EOL) : '')
            . ($this->getReflection()->isFinal ? 'final '    : '')
            . ($this->getReflection()->isAbstract ? 'abstract ' : '')
            . ($this->getReflection()->isInterface ? 'interface' : 'class') . ' ' . $this->getClassBaseName()
            . (($class = $this->getActualParentClassName($parent)) ? (' extends ' . $class) : '')
            . (($interfaces = $this->getInterfaces()) ? (' implements \\' . implode(', \\', $interfaces)) : '')
            . PHP_EOL . '{' . PHP_EOL . '}';
    }

    /**
     * Return source code for regular node
     *
     * @return string
     */
    protected function getRegularSource()
    {
        return \Includes\Utils\FileManager::read($this->getFile());
    }

    /**
     * Return actual parent class name
     *
     * @param \Includes\DataStructure\Graph $parent Node to get class name OPTIONAL
     *
     * @return string
     */
    protected function getActualParentClassName(\Includes\DataStructure\Graph $parent = null)
    {
        $result = null;

        if ($parent) {
            $class = $parent->getClass();
            if ($class) {
                $result = '\\' . $this->prepareClassName($class);
            }
        }

        return $result;
    }

    /**
     * Return namespace by class name
     *
     * @return string
     */
    protected function getActualNamespace()
    {
        list(, $namespace) = $this->getClassNameParts();

        return $namespace ? $this->prepareClassName(implode('\\', $namespace)) : null;
    }

    /**
     * Return base part of the class name
     *
     * @return string
     */
    protected function getClassBaseName()
    {
        list($basename, ) = $this->getClassNameParts();

        return $this->prepareClassName($basename);
    }

    /**
     * Parse class name into parts
     *
     * @return array
     */
    protected function getClassNameParts()
    {
        $parts = explode('\\', $this->getClass());

        return array(array_pop($parts), $parts);
    }

    // }}}

    // {{{ Tags

    /**
     * Get tag info
     *
     * @param string  $name           Tag name
     * @param boolean $forceTokenizer Flag to force tokenizer use (since LC_Dependencies classes could be non-working) OPTIONAL
     *
     * @return array
     */
    public function getTag($name, $forceTokenizer = false)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getTags($forceTokenizer), strtolower($name), true);
    }

    /**
     * Setter
     *
     * @param string $name  Tag name
     * @param array  $value Value to set
     *
     * @return void
     */
    public function setTag($name, array $value)
    {
        $this->getTags();

        $this->tags[$name] = $value;
    }

    /**
     * Parse and return all tags
     *
     * @param boolean $forceTokenizer Flag to force tokenizer use (since LC_Dependencies classes could be non-working) OPTIONAL
     *
     * @return array
     */
    public function getTags($forceTokenizer = false)
    {
        if (!isset($this->tags)) {
            $this->tags = \Includes\Decorator\Utils\Operator::getTags(
                $this->getReflection($forceTokenizer)->docComment
            );

            if (!empty($this->tags['lc_dependencies'][0])) {
                $this->tags['lc_dependencies'] = \Includes\Utils\Converter::parseQuery(
                    $this->tags['lc_dependencies'][0], null, ',', '"\'', false
                );
            }
        }

        return $this->tags;
    }

    /**
     * Clear all tags
     *
     * @param boolean $reversible Flag OPTIONAL
     *
     * @return void
     */
    public function clearTags($reversible = false)
    {
        $this->tags = $reversible ? null : array();
    }

    // }}}

    // {{{ Auxiliary methods

    /**
     * Return the ReflectionClass object for the current node
     *
     * @param boolean $forceTokenizer Flag to force tokenizer use (since LC_Dependencies classes could be non-working) OPTIONAL
     *
     * @return \ReflectionClass
     */
    public function getReflection($forceTokenizer = false)
    {
        if (!isset($this->reflection)) {
            /** @var \Includes\Decorator\Utils\Tokenizer $util */
            $util = '\Includes\Decorator\Utils\Tokenizer';
            $this->reflection = new \StdClass();

            $path = $this->getFile();

            $this->reflection->parentClass = $util::getParentClassName($path);
            $this->reflection->interfaces  = $util::getInterfaces($path);
            $this->reflection->docComment  = $util::getDocBlock($path);
            $this->reflection->isFinal     = $util::isFinal($path);
            $this->reflection->isAbstract  = $util::isAbstract($path);
            $this->reflection->isInterface = (bool) $util::getInterfaceName($path);

            $this->reflection->parentClass = $this->prepareClassName($this->reflection->parentClass);
            $this->reflection->interfaces  = array_map(array($this, 'prepareClassName'), $this->reflection->interfaces);

            $this->reflection->hasStaticConstructor = $util::hasMethod(
                $path,
                \Includes\Decorator\Plugin\StaticRoutines\Main::STATIC_CONSTRUCTOR_METHOD
            );
        }

        return $this->reflection;
    }

    /**
     * Check file 
     * 
     * @return void
     */
    protected function checkFile()
    {
        $path = $this->getFile();
        if (!file_exists($path)) {
            $class = $this->getClass();
            \Includes\Decorator\Utils\CacheManager::fireError(
                'Class ' . $class . ' is not defined in ' . $path . ' file.'
                . ' Class ' . $class . ' is not added to var/run/ directory.'
            );
        }
    }

    /**
     * Prepare class name
     *
     * @param string $class Class name to prepare
     *
     * @return string
     */
    protected function prepareClassName($class)
    {
        return \Includes\Utils\Converter::trimLeadingChars($class, '\\');
    }

    /**
     * For additional info
     *
     * @param \Includes\DataStructure\Graph $node Current node
     *
     * @return string
     */
    protected function drawAdditional(\Includes\DataStructure\Graph $node)
    {
        $result = parent::drawAdditional($node);

        if ($node->getParentClass()) {
            $result .= ' <i>(' . $node->getParentClass() . ')</i>';
        }

        return $result;
    }

    /**
     * Move/copy class file
     *
     * @param string $class New class name
     *
     * @return void
     */
    protected function moveClassFile($class)
    {
        if (!$this->isRoot() && !$this->isRoot($class)) {
            if ($this->getClass()) {
                \Includes\Utils\FileManager::move($this->getFile(), $this->getFile($class));

            } else {
                \Includes\Utils\FileManager::copy($this->getFile($class, LC_DIR_CLASSES), $this->getFile($class));
            }
        }
    }

    // }}}

}
