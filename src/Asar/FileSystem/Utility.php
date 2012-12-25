<?php
/**
 * This file is part of the Asar FileSystem
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\FileSystem;

/**
 * Utility class for filesystem operations
 */
class Utility
{

    /**
     * Finds files with the specified prefix
     *
     * @param string $prefix the prefix of the file path
     *
     * @return array a collection of files with the specfied prefix
     */
    public function findFilesThatStartWith($prefix)
    {
        $foundFiles = array();
        $path = pathinfo($prefix);
        if (is_dir($path['dirname'])) {
            $files = scandir($path['dirname']);
            foreach ($files as $file) {
                if (strpos($file, $path['filename']) === 0) {
                    $foundFiles[] = $path['dirname'] . DIRECTORY_SEPARATOR . $file;
                }
            }
        }

        return $foundFiles;
    }

}