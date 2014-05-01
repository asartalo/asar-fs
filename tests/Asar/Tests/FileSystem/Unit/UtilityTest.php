<?php
/**
 * This file is part of the Asar FileSystem
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


    private function prepareFiles()
    {
        $files = call_user_func_array('array_merge', func_get_args());
        foreach ($files as $file) {
            $this->tfm->newFile($file);
        }
    }

    private function checkCorrectFiles($correctFiles, $otherFiles, $result)
    {
        foreach ($correctFiles as $file) {
            $this->assertContains(
                $this->tfm->getTempDirectory() . DIRECTORY_SEPARATOR
                . $this->getOsPath($file), $result
            );
        }
        foreach ($otherFiles as $file) {
            $this->assertNotContains(
                $this->tfm->getTempDirectory() . DIRECTORY_SEPARATOR
                . $this->getOsPath($file), $result
            );
        }
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
        $otherFiles = array('foo/Four.txt','foo.Five.txt');
        $this->prepareFiles($correctFiles, $otherFiles);

        $result = $this->utility->findFilesThatStartWith(
            $this->tfm->getTempDirectory() . $this->getOsPath('/foo/pre')
        );
        $this->checkCorrectFiles($correctFiles, $otherFiles, $result);
    }

    /**
     * Find files matching a pattern
     */
    public function testFindsFilesMatchingPattern()
    {
        $correctFiles = array(
            'foo/oneFoo.txt',
            'foo/twoFoo.txt',
            'foo/threeBar.txt'
        );
        $otherFiles = array(
            'foo/oneBaz.txt',
            'foo/threeBar.oz',
            'foo/baz/threeBar.txt'
        );
        $this->prepareFiles($correctFiles, $otherFiles);
        $result = $this->utility->findFilesThatMatch(
            $this->tfm->getTempDirectory() . '/foo/*{Foo,Bar}.txt'
        );
        $this->checkCorrectFiles($correctFiles, $otherFiles, $result);
    }


}
