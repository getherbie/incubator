<?php

namespace Herbie;

use Symfony\Component\Yaml\Yaml;

class App
{
    const SORT_ASC = 0;

    protected static $app;
    protected $appDir;
    protected $siteDir;
    protected $vendorDir;
    protected $webDir;
    protected $webUrl;

    protected $alias;
    protected $config;
    protected $content;
    protected $menu;
    protected $tags;
    protected $categories;
    protected $routes = [];
    protected $page;
    protected $hooks;
    protected $plugins;

    protected $pathInfo;
    protected $requestFile;
    protected $timer;

    private $_pages;

    final private function __construct() {}
    final private function __clone() {}

    public function init($siteDir, $vendorDir = "../vendor")
    {
        $this->appDir = realpath(__DIR__);
        $this->siteDir = realpath($siteDir);
        $this->vendorDir = realpath($vendorDir);
        $this->webDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $this->webUrl = $this->detectBaseUrl();
        $this->config = $this->loadConfig();
        #$this->triggerEvent('onConfigLoaded', array(&$this->config));

        $this->alias = $this->loadAlias();
        $this->menu = [];
        $this->page = null;

        // Add custom PSR-4 plugin path to Composer autoloader
        $autoload = require($this->vendorDir . '/autoload.php');
        $autoload->addPsr4('herbie\\sysplugin\\', __DIR__ . '/../plugins/');
        $autoload->addPsr4('HerbiePlugin\\', $this->getConfig('plugins.path'));

        $this->loadPlugins();

        $this->triggerEvent('onTemplateEngineLoading');
#        $this->triggerActionHook('onTemplateEngineLoading');

    }

    public static function instance()
    {
        if (is_null(static::$app)) {
            static::$app = new static();
        }
        return static::$app;
    }

    public function run()
    {
        $this->loadRoutes();

        $this->pathInfo = $this->detectPathInfo();

        $this->requestFile = $this->detectPageFile($this->pathInfo);
        $this->triggerEvent('onRequestFile', array(&$this->requestFile));

        if (empty($this->requestFile)) {
            header("HTTP/1.0 404");
            $alias = "@page/404.txt";
            if(is_file($this->getPathOfAlias($alias))) {
                $this->requestFile = $alias;
            } else {
                throw new \Exception('Error in detectPageFile: page for route "' . $this->pathInfo . '" not found."');
            }
        }

        $this->page = $this->loadPage($this->requestFile);

        $this->triggerEvent('onSinglePageLoaded', array(&$this->page));

        $this->triggerEvent("onPageRendering", [&$this->content, &$this->page]);

#        $this->content = $this->triggerFilterHook('onPageRendering', $this->page);

        return $this->content;
    }

    public function getPage()
    {
        return $this->page;
    }
    
    protected function loadPlugins()
    {
        // add third-party plugins
        $path = $this->getConfig('plugins.path');
        foreach ($this->getConfig('plugins.enable') as $key) {

            $includePath = sprintf("%s/%s/%s.php", $path, $key, $key);
            require($includePath);

            $className = sprintf("\\HerbiePlugin\\%s", ucfirst($key));
            if (class_exists($className)) {
                $plugin = new $className($this);
                $this->plugins[$key] = $plugin;
            } else {
                throw new \Exception("Plugin {$key} could not be loaded.");
            }
        }
    }

    public function loadPage($path)
    {
        $data = $this->parsePageData($path);
        return new Page($data);
    }

    public function loadAlias()
    {
        $alias = [
            '@app' => $this->getConfig('app.path'),
            '@asset' => $this->siteDir . '/assets',
            '@media' => $this->getConfig('media.path'),
            '@page' => $this->getConfig('pages.path'),
            '@plugin' => $this->getConfig('plugins.path'),
            '@site' => $this->siteDir,
            '@vendor' => $this->vendorDir,
            '@web' => $this->getConfig('web.path')
        ];
        uksort($alias, function ($a, $b) {
            return strlen($a) < strlen($b);
        });
        return $alias;
    }

    public function loadConfig()
    {
        // constants used in config files
        if (!defined('APP_PATH')) define('APP_PATH', $this->appDir);
        if (!defined('SITE_PATH')) define('SITE_PATH', $this->siteDir);
        if (!defined('WEB_PATH')) define('WEB_PATH', $this->webDir);
        if (!defined('WEB_URL')) define('WEB_URL', $this->webUrl);

        $defaults = require(__DIR__ . '/../config/defaults.php');
        if (is_file($this->siteDir . '/config/main.php')) {
            $userConfig = require($this->siteDir . '/config.php');
            $defaults = $this->arrayMerge($defaults, $userConfig);
        } elseif (is_file($this->siteDir . '/config/main.yml')) {
            $content = file_get_contents($this->siteDir . '/config/main.yml');
            $content = str_replace(
                ['APP_PATH', 'WEB_PATH', 'WEB_URL', 'SITE_PATH'],
                [$this->appDir, $this->webDir, $this->webUrl, $this->siteDir],
                $content
            );
            $userConfig = Yaml::parse($content);
            $defaults = $this->arrayMerge($defaults, $userConfig);
        }
        return $defaults;
    }

    /**
     * Get value by using dot notation for nested arrays.
     *
     * @example $value = $this->getConfig('twig.extend.functions');
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($name, $default = null)
    {
        $path = explode('.', $name);
        $current = $this->config;
        foreach ($path as $field) {
            if (isset($current) && isset($current[$field])) {
                $current = $current[$field];
            } elseif (is_array($current) && isset($current[$field])) {
                $current = $current[$field];
            } else {
                return $default;
            }
        }
        return $current;
    }

    /**
     * @param array $default
     * @param array $override
     * @return array
     */
    protected function arrayMerge(array $default, array $override)
    {
        foreach ($override as $key => $value) {
            if (is_array($value)) {
                $array = isset($default[$key]) ? $default[$key] : [];
                $default[$key] = $this->arrayMerge($array, $override[$key]);
            } else {
                $default[$key] = $value;
            }
        }
        return $default;
    }

    public function getFiles($alias, $fileExtension = '', $order = self::SORT_ASC)
    {
        $directory = $this->getPathOfAlias($alias);
        $directory = rtrim($directory, '/');

        $result = array();

        // scandir() reads files in alphabetical order
        $files = scandir($directory, $order);
        $fileExtensionLength = strlen($fileExtension);
        if ($files !== false) {
            foreach ($files as $file) {
                // exclude hidden files/dirs starting with a .; this also excludes the special dirs . and ..
                // exclude files ending with a ~ (vim/nano backup) or # (emacs backup)
                if ((substr($file, 0, 1) === '_') || (substr($file, 0, 1) === '.') || in_array(substr($file, -1), array('~', '#'))) {
                    continue;
                }

                if (is_dir($directory . '/' . $file)) {
                    // get files recursively
                    $result = array_merge($result, $this->getFiles($alias . '/' . $file, $fileExtension, $order));
                } elseif (empty($fileExtension) || (substr($file, -$fileExtensionLength) === $fileExtension)) {
                    $result[] = $alias . '/' . $file;
                }
            }
        }

        return $result;
    }

    public function readFiles($alias, $fileExtension = '', $order = self::SORT_ASC)
    {
        $directory = $this->getPathOfAlias($alias);
        $directory = rtrim($directory, '/');

        // scandir() reads files in alphabetical order
        $files = scandir($directory, $order);
        $fileExtensionLength = strlen($fileExtension);
        if ($files !== false) {
            foreach ($files as $file) {
                // exclude hidden files/dirs starting with a .; this also excludes the special dirs . and ..
                // exclude files ending with a ~ (vim/nano backup) or # (emacs backup)
                if ((substr($file, 0, 1) === '_') || (substr($file, 0, 1) === '.') || in_array(substr($file, -1), array('~', '#'))) {
                    continue;
                }

                if (is_dir($directory . '/' . $file)) {
                    // get files recursively
                    foreach ($this->readFiles($alias . '/' . $file, $fileExtension, $order) as $child) {
                        yield $child;
                    }
                } elseif (empty($fileExtension) || (substr($file, -$fileExtensionLength) === $fileExtension)) {
                    #$result[] = $alias . '/' . $file;
                    yield $alias . '/' . $file;
                }
            }
        }

        return;
    }

    /**
     * @return void
     */
    protected function loadRoutes()
    {
        $cacheFile = $this->getConfig('site.path') . "/cache/routes.serialized";
        if (is_file($cacheFile)) {
            $this->routes = unserialize(file_get_contents($cacheFile));
        } else {
            $this->routes = [];
            foreach ($this->getFiles('@page', 'txt') as $alias) {
                $data = $this->parsePageData($alias, ["route", "path"]);
                $this->routes[$data["route"]] = $data["path"];
            }
            #file_put_contents($cacheFile, serialize($this->routes));
        }
    }

    public function parsePageData($alias, array $metaFields = [])
    {
        $path = $this->getPathOfAlias($alias);
        $isEmptyMetaFields = empty($metaFields);

        $data = [];

        /*
        $fields = preg_split('!\n----\s*\n*!', file_get_contents($path));

        foreach ($fields as $field) {
            $pos = strpos($field, ':');
            $key = str_replace(array('-', ' '), '_', strtolower(trim(substr($field, 0, $pos))));

            // Don't add fields with empty keys
            if (empty($key)) continue;

            if ($isEmptyMetaFields || in_array($key, $metaFields)) {
                // add the key object
                $data[$key] = trim(substr($field, $pos + 1));
            }
        }
        */

        preg_match_all("/@([A-Za-z_][^:]*):([^\r\n]*(?:[\r\n]+(?!@[A-Za-z_].*:).*)*)/m", file_get_contents($path), $matches);

        foreach ($matches[1] as $key => $field) {
            if (isset($data[$field])) {
                $data[$field] .= trim($matches[2][$key]);
            } else {
                $data[$field] = trim($matches[2][$key]);
            }
        }

        if (!isset($data['title'])) {
            echo"<pre>";print_r(($data));echo"</pre>";
        }

        if ($isEmptyMetaFields || in_array('path', $metaFields)) {
            $data['path'] = $alias;
        }

        if ($isEmptyMetaFields || in_array('route', $metaFields)) {
            $data['route'] = $this->createRoute($alias, empty($data['keep_extension']));
        }

        if ($isEmptyMetaFields || in_array('parent', $metaFields)) {
            $parent = str_replace(".", "", dirname($data['route']));
            if (empty($parent) && empty($data['route'])) {
                $parent = "__root__";
            }
            $data['parent'] = $parent;
        }

        if (empty($data['modified'])) {
            if ($isEmptyMetaFields || in_array('modified', $metaFields)) {
                $data['modified'] = date('c', filectime($path));
            }
        } elseif (is_numeric($data['modified'])) {
            $data['modified'] = date('c', $data['modified']);
        }

        if (empty($data['date'])) {
            if ($isEmptyMetaFields || in_array('date', $metaFields)) {
                $data['date'] = date('c', filectime($path));
            }
        } elseif (is_numeric($data['date'])) {
            $data['date'] = date('c', $data['date']);
        }

        if (!isset($data['hidden'])) {
            if ($isEmptyMetaFields || in_array('hidden', $metaFields)) {
                $data['hidden'] = !preg_match('/^[0-9]+-/', basename($alias));
            }
        }
        if (!isset($data['layout'])) {
            if ($isEmptyMetaFields || in_array('layout', $metaFields)) {
                $data['layout'] = 'default.html';
            }
        }

        #echo"<pre>";print_r($data);echo"</pre>";

        return $data;
    }

    protected function createRoute($alias, $trimExtension = false)
    {
        $alias = str_replace('@page/', '', $alias);

        // strip left unix AND windows dir separator
        $route = ltrim($alias, '\/');

        // remove leading numbers (sorting) from url segments
        $segments = explode('/', $route);
        foreach ($segments as $i => $segment) {
            $segments[$i] = preg_replace('/^[0-9]+-/', '', $segment);
        }
        $imploded = implode('/', $segments);

        // trim extension
        $pos = strrpos($imploded, '.');
        if ($trimExtension && ($pos !== false)) {
            $imploded = substr($imploded, 0, $pos);
        }

        // remove last "/index" from route
        $route = preg_replace('#\/index$#', '', trim($imploded, '\/'));

        // handle index route
        return ($route == 'index') ? '' : $route;
    }

    public function getPathOfAlias($alias)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }
        return strtr($alias, $this->alias);
    }

    /**
     * @return string
     */
    protected function detectBaseUrl()
    {
        $filename = $this->fetch($_SERVER, 'SCRIPT_FILENAME', '');
        $scriptName = $this->fetch($_SERVER, 'SCRIPT_NAME');
        $phpSelf = $this->fetch($_SERVER, 'PHP_SELF');
        $origScriptName = $this->fetch($_SERVER, 'ORIG_SCRIPT_NAME');

        if ($scriptName !== null && basename($scriptName) === $filename) {
            $baseUrl = $scriptName;
        } elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
            $baseUrl = $phpSelf;
        } elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
            // 1and1 shared hosting compatibility.
            $baseUrl = $origScriptName;
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.
            $baseUrl = '/';
            $basename = basename($filename);
            if ($basename) {
                $path = ($phpSelf ? trim($phpSelf, '/') : '');
                $basePos = strpos($path, $basename) ?: 0;
                $baseUrl .= substr($path, 0, $basePos) . $basename;
            }
        }
        // If the baseUrl is empty, then simply return it.
        if (empty($baseUrl)) {
            return '';
        }
        // Does the base URL have anything in common with the request URI?
        $requestUri = $this->detectRequestUri();
        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }
        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($requestUri, $baseDir)) {
            return $baseDir;
        }
        $truncatedRequestUri = $requestUri;
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }
        $basename = basename($baseUrl);
        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            return '';
        }
        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($requestUri) >= strlen($baseUrl)
            && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        return $baseUrl;
    }

    protected function detectRequestUri()
    {
        $requestUri = null;
        // Check this first so IIS will catch.
        $httpXRewriteUrl = $this->fetch($_SERVER, 'HTTP_X_REWRITE_URL');
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }
        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = $this->fetch($_SERVER, 'HTTP_X_ORIGINAL_URL');
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }
        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $this->fetch($_SERVER, 'IIS_WasUrlRewritten');
        $unencodedUrl = $this->fetch($_SERVER, 'UNENCODED_URL', '');
        if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
            return $unencodedUrl;
        }
        // HTTP proxy requests setup request URI with scheme and host [and port]
        // + the URL path, only use URL path.
        if (!$httpXRewriteUrl) {
            $requestUri = $this->fetch($_SERVER, 'REQUEST_URI');
        }
        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }
        // IIS 5.0, PHP as CGI.
        $origPathInfo = $this->fetch($_SERVER, 'ORIG_PATH_INFO');
        if ($origPathInfo !== null) {
            $queryString = $this->fetch($_SERVER, 'QUERY_STRING', '');
            if ($queryString !== '') {
                $origPathInfo .= '?' . $queryString;
            }
            return $origPathInfo;
        }
        return '/';
    }

    protected function detectPathInfo()
    {
        $baseUrl = $this->detectBaseUrl();

        if (null === ($requestUri = $this->detectRequestUri())) {
            return '/';
        }

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $pathInfo = substr($requestUri, strlen($baseUrl));
        if (null !== $baseUrl && (false === $pathInfo || '' === $pathInfo)) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }

        return (string)$pathInfo;
    }

    public function fetch(array $array, $key, $default = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }

    public function detectPageFile($pathInfo)
    {
        $pathInfo = ltrim($pathInfo, '/');
        foreach ($this->routes as $route => $path) {
            if ($route == $pathInfo) {
                return $path;
            }
        }
        return "";
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getRoute()
    {
        return ltrim("/", $this->pathInfo);
    }

    public function timer($reset = false)
    {
        if ($reset) {
            $this->timer = null;
        }
        if ($this->timer === null) {
            $this->timer = microtime(true);
            return number_format(0, 4);
        }
        return number_format(microtime(true) - $this->timer, 4);
    }

    /**
     * Triggers events on plugins which implement PicoPluginInterface
     *
     * Deprecated events (as used by plugins not implementing
     * {@link PicoPluginInterface}) are triggered by {@link PicoDeprecated}.
     *
     * @see    PicoPluginInterface
     * @see    AbstractPicoPlugin
     * @see    DummyPlugin
     * @param  string $eventName name of the event to trigger
     * @param  array  $params    optional parameters to pass
     * @return void
     */
    public function triggerEvent($eventName, array $params = [])
    {
        foreach ($this->plugins as $plugin) {
            if (is_a($plugin, '\\Herbie\\PluginInterface')) {
                $plugin->handleEvent($eventName, $params);
            }
        }
    }

    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    public function getPlugin($name)
    {
        return $this->plugins[$name];
    }

}




function debug($what)
{
    echo"<pre>";
    print_r($what);
    echo"</pre>";
}
