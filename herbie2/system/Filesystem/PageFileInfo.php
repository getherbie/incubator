<?php

/**
 * This file is part of Herbie2.
 *
 * (c) Thomas Breuss <www.tebe.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Herbie2\Filesystem;

use Herbie2\Yaml;

class PageFileInfo extends \SplFileInfo
{
    #private $file;
    public static $alias = [
        '@page' => '/web/www/getherbie/website2/site/pages/',
    ];

    private $path;
    private $relativePath;
    private $relativePathname;

    /**
     * Constructor
     *
     * @param string $file             The file name
     * @param string $relativePath     The relative path
     * @param string $relativePathname The relative path name
     */
    public function __construct($path)
    {
        $file = str_replace(array_keys(self::$alias), array_values(self::$alias), $path);
        $relativePathname = ltrim(str_replace(array_keys(self::$alias), '', $path), '/');

        parent::__construct($file);
        $this->path = $path;
        $this->relativePathname = $relativePathname;

        $this->setInfoClass(PageFileInfo::CLASS);
        #$this->setFileClass(PageFileObject::CLASS);
    }

    /**
     * Returns the relative path
     *
     * @return string the relative path
     */
    public function getRelativePath()
    {
        return dirname($this->relativePath);
    }

    /**
     * Returns the relative path name
     *
     * @return string the relative path name
     */
    public function getRelativePathname()
    {
        return $this->relativePathname;
    }

    /**
     * @return bool
     */
    public function isDot()
    {
        return in_array($this->getBasename(), ['.', '..']);
    }

    /**
     * @param bool $trimExtension
     * @return string
     */
    public function createRoute($trimExtension = false)
    {

        // strip left unix AND windows dir separator
        $route = ltrim($this->getRelativePathname(), '\/');

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

    public function isHidden()
    {
        return !preg_match('/^[0-9]+-/', $this->getBasename());
    }

    public function getIndexFiles()
    {
        $files = [];
        $pattern = $this->getRealPath() . '/*index.*';
        foreach (glob($pattern) as $file) {
            $basename = pathinfo($file, PATHINFO_BASENAME);
            if (preg_match("/^[0-9]+[-]|^index/",$basename)) {
                $relativePath = $this->getRelativePathname();
                $relativePathname = $this->getRelativePathname() . '/' . $basename;
                #echo "$file<br>$relativePath<br>$relativePathname<br><br>";
                $fileInfo = new static('@page/' . $relativePathname);
                $files[] = $fileInfo;
            }
        }
        return $files;
    }

    public function getFrontmatter()
    {
        $data = $this->openFile()->readFrontmatter();

        $data = $this->hydrateData($data);

        return $data;
    }

    public function openFile ($open_mode = "r", $use_include_path = false, $context = NULL)
    {
        return new PageFileObject($this->getRealPath(), $this->getRelativePath(), $this->getRelativePathname());
    }

    protected function hydrateData($data)
    {
        $data['path'] = $this->path;
        $data['route'] = $this->createRoute(empty($data['keep_extension']));

        if (empty($data['modified'])) {
            $data['modified'] = date('c', $this->getMTime());
        }
        if (empty($data['date'])) {
            $data['date'] = date('c', $this->getCTime());
        }
        if (!isset($data['hidden'])) {
            $data['hidden'] = $this->isHidden();
        }
        return $data;
    }

    /**
     * @param string $alias
     * @return string
     * @throws ResourceNotFoundException
     */
    public function getContents()
    {
        // suppress E_WARNING since we throw an exception on error
        $contents = @file_get_contents($this->path);
        if (false === $contents) {
            throw new ResourceNotFoundException('Page "' . $this->path . '" does not exist.');
        }
        return $contents;
    }

    /**
     * @param string $alias
     * @return array
     */
    public function getRawData()
    {
        $content = $this->readFile();
        return $this->parseContent($content);
    }

    /**
     * @param string $content
     * @return array
     * @throws \Exception
     */
    protected function parseContent($content)
    {
        if(!defined('UTF8_BOM')) {
            define('UTF8_BOM', chr(0xEF).chr(0xBB).chr(0xBF));
        }

        $yaml = '';
        $segments = [];

        $matched = preg_match('/^['.UTF8_BOM.']*-{3}\r?\n(.*)\r?\n-{3}\R(.*)/ms', $content, $matches);

        if ($matched === 1 && count($matches) == 3) {
            $yaml = $matches[1];

            $splitted = preg_split('/^-{3} (.+) -{3}\R?$/m', $matches[2], -1, PREG_SPLIT_DELIM_CAPTURE);

            $count = count($splitted);
            if ($count %2 == 0) {
                throw new \Exception('Fehler beim Auslesen der Seite.');
            }

            $segments[] = array_shift($splitted);
            $ct_splitted = count($splitted);
            for ($i=0; $i<$ct_splitted; $i=$i+2) {
                $key = $splitted[$i];
                $value = $splitted[$i+1];
                if (array_key_exists($key, $segments)) {
                    $segments[$key] .= $value;
                } else {
                    $segments[$key] = $value;
                }
            }

            $i = 0;
            $last = count($segments) - 1;
            foreach ($segments as $key => $segment) {
                $segments[$key] = ($i == $last) ? $segment : preg_replace('/\R?$/', '', $segment, 1);
                $i++;
            }
        }

        $yaml = Yaml::parse($yaml);
        $yaml = $this->hydrateData($yaml);

        return [$yaml, $segments];
    }

    /**
     * @param string $alias
     * @return string
     * @throws ResourceNotFoundException
     */
    protected function readFile()
    {
        // suppress E_WARNING since we throw an exception on error
        $contents = @file_get_contents($this->getRealPath());
        if (false === $contents) {
            throw new \Exception('Page "' . $this->path . '" does not exist.');
        }
        return $contents;
    }

}
