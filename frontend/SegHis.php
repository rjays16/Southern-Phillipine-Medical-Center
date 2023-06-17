<?php
/**
 * Frontend application used by SegHIS
 *
 * @uses CWebApplication
 * @version $id$
 * @copyright Copyright &copy; 2015. Segworks Technologies Corporation
 * @author Alvin Quinones <ajqmuinones@segworks.com>
 */
use League\Event\Emitter;
use League\Event\ListenerInterface;
use League\Event\ListenerProviderInterface;

class SegHis extends CWebApplication
{

    protected $_rootPath;

    /**
     * The dependency injection container
     *
     * @var Pimple $diContainer
     */
    protected $diContainer;
    /**
     *
     * @var array $bindings
     */
    protected $bindings;
    /**
     * @var
     */
    protected $listenerProviders = array();    

    /**
     *
     */
    public function setRootPath($path)
    {
        $this->_rootPath = $path;
    }

    /**
     *
     */
    public function getRootPath()
    {
        return $this->_rootPath;
    }

    /**
     *
     * @param string $moduleId
     *
     * @return boolean
     */
    public function getModuleConfig($moduleId)
    {
        $t = $this->getModules();

        return isset($t[$moduleId]) ? $t[$moduleId] : false;
    }

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->initializeDiContainer();
        $this->initializeDiBindings();
        $this->initializeListeners();
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws CException
     */
    public function getBinding($key)
    {
        if (isset($this->diContainer[$key])) {
            return $this->diContainer[$key];
        } else {
            throw new CException('The binding "' . $key . '" is not recognized');
        }
    }

    /**
     * Helps the system to set the default homeUrl to root.
     *
     * @param CController $controller
     * @param CAction $action
     *
     * @return bool
     *
     * @author Jolly Caralos
     */
    public function beforeControllerAction($controller, $action)
    {
        // Gii module doesn't have this property
        if (isset($controller->homeUrl)) {
            /** @var WebModule $module */
            $module = $this->getModule($this->defaultController);
            if (empty($module)) {
                $controller->homeUrl = '/' . $this->defaultController
                    . '/' . $controller->defaultAction;
            } else {
                $controller->homeUrl = '/' . $module->getId()
                    . '/' . $module->defaultController
                    . '/' . $controller->defaultAction;
            }
        }

        return parent::beforeControllerAction($controller, $action);
    }

    /**
     *
     */
    protected function initializeDiContainer()
    {
        \Yii::import('application.vendors.Pimple.Pimple');
        $this->diContainer = new Pimple();
    }

    /**
     *
     */
    protected function initializeDiBindings()
    {
        if (!empty($this->bindings)) {
            foreach ($this->bindings as $key => $binding) {
                $binding = CMap::mergeArray(array(
                    'bindingType' => 'instance',
                    'bindTo' => null,
                ), $binding);
                if ($binding['bindingType'] == 'instance') {
                    $this->diContainer[$key] = function () use ($binding) {
                        return Yii::createComponent($binding['bindTo']);
                    };
                } elseif ($binding['bindingType'] == 'singleton') {
                    $bindTo = $binding['bindTo'];
                    if (is_array($bindTo)) {
                        $this->diContainer[$key] = $this->diContainer->share(function () use ($bindTo) {
                            return Yii::createComponent($bindTo);
                        });
                    } elseif (is_callable($bindTo)) {
                        $this->diContainer[$key] = $this->diContainer->share($bindTo);
                    } else {
                        // Illegal binding! Do nothing???
                        // @todo Maybe throw some exception?
                    }
                } else {
                    if (is_callable($binding['bindTo'])) {
                        $this->diContainer[$key] = $this->diContainer->protect($binding['bindTo']);
                    } else {
                        $this->diContainer[$key] = $binding['bindTo'];
                    }
                }
            }
        }
    }

    /**
     *
     */
    protected function initializeListeners()
    {
        foreach ($this->getModules() as $module => $config) {
            $this->initializeModuleListeners($module, $config);
        }
    }

    /**
     * Expects modules that register listeners to have a getListeners public
     * static method.
     *
     * @param $module
     * @param array $moduleConfig
     *
     * @throws CException
     */
    protected function initializeModuleListeners($module, $moduleConfig = array())
    {
        if (empty($moduleConfig) && is_int($module)) {
            $module = $moduleConfig;
            $moduleConfig = array();
        }

        if (isset($moduleConfig['modules']) && is_array($moduleConfig['modules'])) {
            foreach ($moduleConfig['modules'] as $subModule => $subModuleConfig) {
                $this->initializeModuleListeners($subModule, $subModuleConfig);
            }
        }

        $classPath = @$moduleConfig['class'];                
        if (!$classPath) {
            $classPath = $module . '.' . ucfirst($module) . 'Module';
        }

        if (!class_exists($classPath)) {
            \Yii::import($classPath);
        }

        $className = $classPath;
        if (strpos($className, '.') !== false) {
            $className = substr($classPath, strrpos($className, '.') + 1);
        }
        
        if (method_exists($className, 'getListeners')) {
            $listeners = call_user_func(array($className, 'getListeners'));
            /** @var Emitter $emitter */
            $emitter = $this->getComponent('emitter');                                   
            foreach ($listeners as $event => $listener) {                             
                if (is_int($event) && $listener instanceof ListenerProviderInterface) {                                                             
                    $emitter->useListenerProvider($listener);
                } elseif ($listener instanceof ListenerInterface || is_callable($listener)) {                                                        
                    $emitter->addListener($event, $listener);
                }
            }
        }
    }    
}
