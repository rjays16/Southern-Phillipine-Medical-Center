<?php
/**
 * ConsoleApplication
 *
 * @author Bong Trazo
 * 
 * @copyright Copyright &copy; Segworks Technologies Corporation 2012
 *            Copied from ConsoleApplication.php of Alvin Quinones
 */
use League\Event\Emitter;
use League\Event\ListenerInterface;
use League\Event\ListenerProviderInterface;

/**
 *
 */
class ConsoleSocketApplication extends CConsoleApplication
{
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
     * @param string $moduleId
     * @return boolean
     */
    public function getModuleConfig($moduleId)
    {
        $t = $this->getModules();
        return isset($t[$moduleId]) ? $t[$moduleId] : FALSE;
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
                    'bindTo' => null
                ), $binding);
                if ($binding['bindingType'] == 'instance') {
                    $this->diContainer[$key] = function() use ($binding) {
                        return Yii::createComponent($binding['bindTo']);
                    };
                } elseif ($binding['bindingType'] == 'singleton') {
                    $bindTo = $binding['bindTo'];
                    if (is_array($bindTo)) {
                        $this->diContainer[$key] = $this->diContainer->share(function() use ($bindTo) {
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
            $classPath = $module.'.'.ucfirst($module).'Module';
        }

        if (!class_exists($classPath)) {
            \Yii::import($classPath);
        }    
        
        $className = $classPath;
        if (strpos($className, '.') !== false) {
            $className = substr($classPath, strrpos($className, '.')+1);
        }      
        
        if (method_exists($className, 'getListeners')) {
            $listeners = call_user_func(array($className, 'getListeners'));
            /** @var Emitter $emitter */                                                
//            fwrite(STDERR, print_r($listeners, true)."\n");             
            $emitter = $this->getComponent('emitter');             
            foreach ($listeners as $event => $listener) {                
                if (is_int($event) && $listener instanceof ListenerProviderInterface) {
                    $emitter->useListenerProvider($listener);
                }
                elseif ($listener instanceof ListenerInterface || is_callable($listener)) {
                    $emitter->addListener($event, $listener);
                }

            }
        }        
//        fwrite(STDERR, $classPath."\n");        
    }
}