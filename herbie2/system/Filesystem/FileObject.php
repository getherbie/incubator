<?php

namespace Herbie2\Filesystem;

use Herbie2\Yaml;

class FileObject extends \SplFileObject
{

    /**
     * @return array
     */
    public function readFrontmatter()
    {
        if(!defined('UTF8_BOM')) {
            define('UTF8_BOM', chr(0xEF).chr(0xBB).chr(0xBF));
        }

        $content = '';

        $i = 0;
        foreach ($this as $line) {
            // strip BOM from the beginning and \n and \r from end of line
            $line = rtrim(ltrim($line, UTF8_BOM), "\n\r");
            if (preg_match('/^---$/', $line)) {
                $i++;
                continue;
            }
            if ($i > 1) {
                break;
            }
            if ($i == 1) {
                // add PHP_EOL to end of line
                $content .= $line . PHP_EOL;
            }
        }

        return (array) Yaml::parse($content);
    }

}
