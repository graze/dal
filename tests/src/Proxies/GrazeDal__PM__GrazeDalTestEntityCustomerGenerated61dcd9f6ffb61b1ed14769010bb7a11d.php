<?php

namespace Graze\Dal\__PM__\Graze\Dal\Test\Entity\Customer;

class Generated61dcd9f6ffb61b1ed14769010bb7a11d extends \Graze\Dal\Test\Entity\Customer implements \ProxyManager\Proxy\GhostObjectInterface
{

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer56698bc8d4357146276760 = null;

    /**
     * @var bool tracks initialization status - true while the object is initializing
     */
    private $initializationTracker56698bc8d437d115064227 = false;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties56698bc8d42cc448672697 = array(
        
    );

    /**
     * @var mixed[] map of default property values of the parent class
     */
    private static $publicPropertiesDefaults56698bc8d431a691381006 = array(
        
    );

    private static $signature61dcd9f6ffb61b1ed14769010bb7a11d = 'YTozOntzOjk6ImNsYXNzTmFtZSI7czozMDoiR3JhemVcRGFsXFRlc3RcRW50aXR5XEN1c3RvbWVyIjtzOjc6ImZhY3RvcnkiO3M6NDQ6IlByb3h5TWFuYWdlclxGYWN0b3J5XExhenlMb2FkaW5nR2hvc3RGYWN0b3J5IjtzOjE5OiJwcm94eU1hbmFnZXJWZXJzaW9uIjtzOjU6IjEuMC4wIjt9';

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setFirstName($firstName)
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('setFirstName', array('firstName' => $firstName));

        return parent::setFirstName($firstName);
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('getFirstName', array());

        return parent::getFirstName();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastName($lastName)
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('setLastName', array('lastName' => $lastName));

        return parent::setLastName($lastName);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName()
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('getLastName', array());

        return parent::getLastName();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrders()
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('getOrders', array());

        return parent::getOrders();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrders(\Doctrine\Common\Collections\ArrayCollection $orders)
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('setOrders', array('orders' => $orders));

        return parent::setOrders($orders);
    }

    /**
     * {@inheritDoc}
     */
    public function addOrder(\Graze\Dal\Test\Entity\Order $orders)
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('addOrder', array('orders' => $orders));

        return parent::addOrder($orders);
    }

    /**
     * Triggers initialization logic for this ghost object
     */
    private function callInitializer56698bc8d43b3183726502($methodName, array $parameters)
    {
        if ($this->initializationTracker56698bc8d437d115064227 || ! $this->initializer56698bc8d4357146276760) {
            return;
        }

        $this->initializationTracker56698bc8d437d115064227 = true;

        foreach (self::$publicPropertiesDefaults56698bc8d431a691381006 as $key => $default) {
            $this->$key = $default;
        }

        $this->initializer56698bc8d4357146276760->__invoke($this, $methodName, $parameters, $this->initializer56698bc8d4357146276760);

        $this->initializationTracker56698bc8d437d115064227 = false;
    }

    /**
     * @override constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public function __construct($initializer)
    {
        $this->initializer56698bc8d4357146276760 = $initializer;
    }

    /**
     * @param string $name
     */
    public function & __get($name)
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('__get', array('name' => $name));

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
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('__set', array('name' => $name, 'value' => $value));

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
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('__isset', array('name' => $name));

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
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('__unset', array('name' => $name));

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
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('__clone', array());
    }

    public function __sleep()
    {
        $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('__sleep', array());

        return array_keys((array) $this);
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyInitializer(\Closure $initializer = null)
    {
        $this->initializer56698bc8d4357146276760 = $initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyInitializer()
    {
        return $this->initializer56698bc8d4357146276760;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeProxy()
    {
        return $this->initializer56698bc8d4357146276760 && $this->callInitializer56698bc8d43b3183726502('initializeProxy', array());
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyInitialized()
    {
        return ! $this->initializer56698bc8d4357146276760;
    }


}
