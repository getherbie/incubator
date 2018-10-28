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

class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    public function __construct($path)
    {
        $alias = [
            '@page' => '/web/www/getherbie/website2/site/pages/',
        ];
        $file = str_replace(array_keys($alias), array_values($alias), $path);
        parent::__construct($file);
    }

    /**
     * Return an instance of PageFileInfo with support for relative paths
     *
     * @return PageFileInfo File information
     */
    public function current()
    {
        $fileInfo = new PageFileInfo('@page/' . $this->getSubPathname());
        return $fileInfo;
    }
}
