<?php

namespace Herbie;

interface PluginInterface
{
    public function __construct(App $app);

    /**
     * Handles a event that was triggered by App
     *
     * @param  string $eventName name of the triggered event
     * @param  array  $params    passed parameters
     * @return void
     */
    public function handleEvent($eventName, array &$params);

    /**
     * Enables or disables this plugin
     *
     * @see    PluginInterface::isEnabled()
     * @see    PluginInterface::isStatusChanged()
     * @param  boolean $enabled     enable (true) or disable (false) this plugin
     * @param  boolean $recursive   when true, enable or disable recursively
     *     In other words, if you enable a plugin, all required plugins are
     *     enabled, too. When disabling a plugin, all depending plugins are
     *     disabled likewise. Recursive operations are only performed as long
     *     as a plugin wasn't enabled/disabled manually. This parameter is
     *     optional and defaults to true.
     * @param  boolean $auto        enable or disable to fulfill a dependency
     *     This parameter is optional and defaults to false.
     * @return void
     * @throws RuntimeException     thrown when a dependency fails
     */
    public function setEnabled($enabled, $recursive = true, $auto = false);

    /**
     * Returns true if this plugin is enabled, false otherwise
     *
     * @see    PluginInterface::setEnabled()
     * @return boolean plugin is enabled (true) or disabled (false)
     */
    public function isEnabled();

    /**
     * Returns true if the plugin was ever enabled/disabled manually
     *
     * @see    PluginInterface::setEnabled()
     * @return boolean plugin is in its default state (true), false otherwise
     */
    public function isStatusChanged();

    /**
     * Returns a list of names of plugins required by this plugin
     *
     * @return string[] required plugins
     */
    public function getDependencies();

    /**
     * Returns a list of plugins which depend on this plugin
     *
     * @return object[] dependant plugins
     */
    public function getDependants();

    /**
     * Returns the plugins instance of
     *
     * @see
     * @return App the plugins instance of App
     */
    public function getApp();
}
