<?php
/**
 * This file is part of the Asar Web Framework
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\FileSystem\Unit;

use Asar\TestHelper\TestCase;
use Asar\FileSystem\Utility;

/**
 * Specifications for Asar\FileSystem\Utility;
 */
class UtilityTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->tempdir = $this->getTempDir();
        $this->tfm = $this->getTFM();
        $this->clearTestTempDirectory();

        $this->utility = new Utility;
    }

    /**
     * Teardown
     */
    public function tearDown()
    {
        $this->clearTestTempDirectory();
    }

    /**
     * Finds files that starts with specified prefix
     */
    public function testFindsFilesWithPrefix()
    {
        $correctFiles = array(
            'foo/preOne.txt',
            'foo/preTwo.txt',
            'foo/preThree.txt'
        );
        foreach ($correctFiles as $file) {
            $this->tfm->newFile($file);
        }
        // other files...
        $this->tfm->newFile('foo/Four.txt');
        $this->tfm->newFile('foo.Five.txt');
        $result = $this->utility->findFilesThatStartWith(
            $this->tfm->getTempDirectory() . $this->getOsPath('/foo/pre')
        );
        foreach ($correctFiles as $file) {
            $this->assertContains(
                $this->tfm->getTempDirectory() . DIRECTORY_SEPARATOR
                . $this->getOsPath($file), $result
            );
        }
    }

}