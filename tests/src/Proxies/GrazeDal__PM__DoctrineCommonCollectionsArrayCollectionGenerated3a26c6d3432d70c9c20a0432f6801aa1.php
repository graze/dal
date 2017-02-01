<?php

namespace Graze\Dal\__PM__\Doctrine\Common\Collections\ArrayCollection;

class Generated3a26c6d3432d70c9c20a0432f6801aa1 extends \Doctrine\Common\Collections\ArrayCollection implements \ProxyManager\Proxy\GhostObjectInterface
{

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer56698bc873dd0266033939 = null;

    /**
     * @var bool tracks initialization status - true while the object is initializing
     */
    private $initializationTracker56698bc874d4b582186363 = false;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties56698bc86fc98739670482 = array(
        
    );

    /**
     * @var mixed[] map of default property values of the parent class
     */
    private static $publicPropertiesDefaults56698bc872bd2153750103 = array(
        
    );

    private static $signature3a26c6d3432d70c9c20a0432f6801aa1 = 'YTozOntzOjk6ImNsYXNzTmFtZSI7czo0MzoiRG9jdHJpbmVcQ29tbW9uXENvbGxlY3Rpb25zXEFycmF5Q29sbGVjdGlvbiI7czo3OiJmYWN0b3J5IjtzOjQ0OiJQcm94eU1hbmFnZXJcRmFjdG9yeVxMYXp5TG9hZGluZ0dob3N0RmFjdG9yeSI7czoxOToicHJveHlNYW5hZ2VyVmVyc2lvbiI7czo1OiIxLjAuMCI7fQ==';

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('toArray', array());

        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('first', array());

        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('last', array());

        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('key', array());

        return parent::key();
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('next', array());

        return parent::next();
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('current', array());

        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('remove', array('key' => $key));

        return parent::remove($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('removeElement', array('element' => $element));

        return parent::removeElement($element);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('offsetExists', array('offset' => $offset));

        return parent::offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('offsetGet', array('offset' => $offset));

        return parent::offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('offsetSet', array('offset' => $offset, 'value' => $value));

        return parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('offsetUnset', array('offset' => $offset));

        return parent::offsetUnset($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('containsKey', array('key' => $key));

        return parent::containsKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('contains', array('element' => $element));

        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(\Closure $p)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('exists', array('p' => $p));

        return parent::exists($p);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('indexOf', array('element' => $element));

        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('get', array('key' => $key));

        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('getKeys', array());

        return parent::getKeys();
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('getValues', array());

        return parent::getValues();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('count', array());

        return parent::count();
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('set', array('key' => $key, 'value' => $value));

        return parent::set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('add', array('value' => $value));

        return parent::add($value);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('isEmpty', array());

        return parent::isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('getIterator', array());

        return parent::getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function map(\Closure $func)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('map', array('func' => $func));

        return parent::map($func);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(\Closure $p)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('filter', array('p' => $p));

        return parent::filter($p);
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(\Closure $p)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('forAll', array('p' => $p));

        return parent::forAll($p);
    }

    /**
     * {@inheritDoc}
     */
    public function partition(\Closure $p)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('partition', array('p' => $p));

        return parent::partition($p);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__toString', array());

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('clear', array());

        return parent::clear();
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('slice', array('offset' => $offset, 'length' => $length));

        return parent::slice($offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function matching(\Doctrine\Common\Collections\Criteria $criteria)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('matching', array('criteria' => $criteria));

        return parent::matching($criteria);
    }

    /**
     * Triggers initialization logic for this ghost object
     */
    private function callInitializer56698bc8776bd327192686($methodName, array $parameters)
    {
        if ($this->initializationTracker56698bc874d4b582186363 || ! $this->initializer56698bc873dd0266033939) {
            return;
        }

        $this->initializationTracker56698bc874d4b582186363 = true;

        foreach (self::$publicPropertiesDefaults56698bc872bd2153750103 as $key => $default) {
            $this->$key = $default;
        }

        $this->initializer56698bc873dd0266033939->__invoke($this, $methodName, $parameters, $this->initializer56698bc873dd0266033939);

        $this->initializationTracker56698bc874d4b582186363 = false;
    }

    /**
     * @override constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public function __construct($initializer)
    {
        $this->initializer56698bc873dd0266033939 = $initializer;
    }

    /**
     * @param string $name
     */
    public function & __get($name)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__get', array('name' => $name));

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this;

            $backtrace = debug_backtrace(false);
            trigger_error('Undefined property: ' . get_parent_class($this) . '::$' . $name . ' in ' . $backtrace[0]['file'] . ' on line ' . $backtrace[0]['line'], \E_USER_NOTICE);
            return $targetObject->$name;;
            return;
        }

        $targetObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen(get_parent_class($this)), get_parent_class($this)));
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    /**
     * @param string $name
     */
    public function __set($name, $value)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__set', array('name' => $name, 'value' => $value));

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this;

            return $targetObject->$name = $value;;
            return;
        }

        $targetObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen(get_parent_class($this)), get_parent_class($this)));
        $accessor = function & () use ($targetObject, $name, $value) {
            return $targetObject->$name = $value;
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__isset', array('name' => $name));

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this;

            return isset($targetObject->$name);;
            return;
        }

        $targetObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen(get_parent_class($this)), get_parent_class($this)));
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__unset', array('name' => $name));

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this;

            unset($targetObject->$name);;
            return;
        }

        $targetObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen(get_parent_class($this)), get_parent_class($this)));
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __clone()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__clone', array());
    }

    public function __sleep()
    {
        $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('__sleep', array());

        return array_keys((array) $this);
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyInitializer(\Closure $initializer = null)
    {
        $this->initializer56698bc873dd0266033939 = $initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyInitializer()
    {
        return $this->initializer56698bc873dd0266033939;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeProxy()
    {
        return $this->initializer56698bc873dd0266033939 && $this->callInitializer56698bc8776bd327192686('initializeProxy', array());
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyInitialized()
    {
        return ! $this->initializer56698bc873dd0266033939;
    }


}
