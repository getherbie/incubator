<?php

namespace Herbie;

/**
 * Abstract class to extend from when implementing a Herbie plugin
 *
 * @see PluginInterface
 */
abstract class Plugin implements PluginInterface
{
    /**
     * Current instance of Herbie
     *
     * @see PluginInterface::getApp()
     * @var Herbie
     */
    protected $app;

    /**
     * Boolean indicating if this plugin is enabled (true) or disabled (false)
     *
     * @see PluginInterface::isEnabled()
     * @see PluginInterface::setEnabled()
     * @var boolean
     */
    protected $enabled = true;

    /**
     * Boolean indicating if this plugin was ever enabled/disabled manually
     *
     * @see PluginInterface::isStatusChanged()
     * @var boolean
     */
    protected $statusChanged = false;

    /**
     * List of plugins which this plugin depends on
     *
     * @see AbstractPlugin::checkDependencies()
     * @see PluginInterface::getDependencies()
     * @var string[]
     */
    protected $dependsOn = array();

    /**
     * List of plugin which depend on this plugin
     *
     * @see AbstractPlugin::checkDependants()
     * @see PluginInterface::getDependants()
     * @var object[]
     */
    private $dependants;

    /**
     * @see PluginInterface::__construct()
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @see PluginInterface::handleEvent()
     */
    public function handleEvent($eventName, array &$params)
    {
        // plugins can be enabled/disabled using the config
        if ($eventName === 'onConfigLoaded') {
            $pluginEnabled = $this->getConfig(get_called_class() . '.enabled');
            if ($pluginEnabled !== null) {
                $this->setEnabled($pluginEnabled);
            } else {
                $pluginConfig = $this->getConfig(get_called_class());
                if (is_array($pluginConfig) && isset($pluginConfig['enabled'])) {
                    $this->setEnabled($pluginConfig['enabled']);
                } elseif ($this->enabled) {
                    // make sure dependencies are already fulfilled,
                    // otherwise the plugin needs to be enabled manually
                    try {
                        $this->checkDependencies(false);
                    } catch (RuntimeException $e) {
                        $this->enabled = false;
                    }
                }
            }
        }

        if ($this->isEnabled() || ($eventName === 'onPluginsLoaded')) {
            if (method_exists($this, $eventName)) {
                call_user_func_array(array($this, $eventName), $params);
            }
        }
    }

    /**
     * @see PluginInterface::setEnabled()
     */
    public function setEnabled($enabled, $recursive = true, $auto = false)
    {
        $this->statusChanged = (!$this->statusChanged) ? !$auto : true;
        $this->enabled = (bool) $enabled;

        if ($enabled) {
            $this->checkDependencies($recursive);
        } else {
            $this->checkDependants($recursive);
        }
    }

    /**
     * @see PluginInterface::isEnabled()
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @see PluginInterface::isStatusChanged()
     */
    public function isStatusChanged()
    {
        return $this->statusChanged;
    }

    /**
     * @see PluginInterface::getApp()
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Passes all not satisfiable method calls to Herbie
     *
     * @see    App
     * @param  string $methodName name of the method to call
     * @param  array  $params     parameters to pass
     * @return mixed              return value of the called method
     */
    public function __call($methodName, array $params)
    {
        if (method_exists($this->getApp(), $methodName)) {
            return call_user_func_array(array($this->getApp(), $methodName), $params);
        }

        throw new BadMethodCallException(
            'Call to undefined method ' . get_class($this->getApp()) . '::' . $methodName . '() '
            . 'through ' . get_called_class() . '::__call()'
        );
    }

    /**
     * Enables all plugins which this plugin depends on
     *
     * @see    PluginInterface::getDependencies()
     * @param  boolean $recursive enable required plugins automatically
     * @return void
     * @throws RuntimeException   thrown when a dependency fails
     */
    protected function checkDependencies($recursive)
    {
        foreach ($this->getDependencies() as $pluginName) {
            try {
                $plugin = $this->getPlugin($pluginName);
            } catch (RuntimeException $e) {
                throw new RuntimeException(
                    "Unable to enable plugin '" . get_called_class() . "': "
                    . "Required plugin '" . $pluginName . "' not found"
                );
            }

            // plugins which don't implement PluginInterface are always enabled
            if (is_a($plugin, 'PluginInterface') && !$plugin->isEnabled()) {
                if ($recursive) {
                    if (!$plugin->isStatusChanged()) {
                        $plugin->setEnabled(true, true, true);
                    } else {
                        throw new RuntimeException(
                            "Unable to enable plugin '" . get_called_class() . "': "
                            . "Required plugin '" . $pluginName . "' was disabled manually"
                        );
                    }
                } else {
                    throw new RuntimeException(
                        "Unable to enable plugin '" . get_called_class() . "': "
                        . "Required plugin '" . $pluginName . "' is disabled"
                    );
                }
            }
        }
    }

    /**
     * @see PluginInterface::getDependencies()
     */
    public function getDependencies()
    {
        return (array) $this->dependsOn;
    }

    /**
     * Disables all plugins which depend on this plugin
     *
     * @see    PluginInterface::getDependants()
     * @param  boolean $recursive disabled dependant plugins automatically
     * @return void
     * @throws RuntimeException   thrown when a dependency fails
     */
    protected function checkDependants($recursive)
    {
        $dependants = $this->getDependants();
        if (!empty($dependants)) {
            if ($recursive) {
                foreach ($this->getDependants() as $pluginName => $plugin) {
                    if ($plugin->isEnabled()) {
                        if (!$plugin->isStatusChanged()) {
                            $plugin->setEnabled(false, true, true);
                        } else {
                            throw new RuntimeException(
                                "Unable to disable plugin '" . get_called_class() . "': "
                                . "Required by manually enabled plugin '" . $pluginName . "'"
                            );
                        }
                    }
                }
            } else {
                $dependantsList = 'plugin' . ((count($dependants) > 1) ? 's' : '') . ' ';
                $dependantsList .= "'" . implode("', '", array_keys($dependants)) . "'";
                throw new RuntimeException(
                    "Unable to disable plugin '" . get_called_class() . "': "
                    . "Required by " . $dependantsList
                );
            }
        }
    }

    /**
     * @see PluginInterface::getDependants()
     */
    public function getDependants()
    {
        if ($this->dependants === null) {
            $this->dependants = array();
            foreach ($this->getPlugins() as $pluginName => $plugin) {
                // only plugins which implement PluginInterface support dependencies
                if (is_a($plugin, 'PluginInterface')) {
                    $dependencies = $plugin->getDependencies();
                    if (in_array(get_called_class(), $dependencies)) {
                        $this->dependants[$pluginName] = $plugin;
                    }
                }
            }
        }

        return $this->dependants;
    }
}
