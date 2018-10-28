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

class FileFilterCallback
{

    /**
     * @var array
     */
    private $extensions;

    /**
     * @param array $extensions
     */
    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     *
     * @param PageFileInfo $file
     * @return boolean
     */
    public function filter(PageFileInfo $file)
    {
        $firstChar = substr($file->getFileName(), 0, 1);
        if (in_array($firstChar, ['.', '_', '-'])) {
            return false;
        }

        if ($file->isDir()) {
            return true;
        }

        if (!in_array($file->getExtension(), $this->extensions)) {
            return false;
        }

        return true;
    }

    public function call(array $file)
    {
        $firstChar = substr($file['name'], 0, 1);
        if (in_array($firstChar, ['.', '_', '-'])) {
            return false;
        }

        if ($file['isdir']) {
            return true;
        }

        if (!in_array($file['extension'], $this->extensions)) {
            return false;
        }

        return true;
    }

}
