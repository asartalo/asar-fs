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
use Asar\FileSystem\File;

/**
 * Specifications for Asar\FileSystem\File;
 */
class FileTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->tempdir = $this->getTempDir();
        $this->tfm = $this->getTFM();
        $this->clearTestTempDirectory();
    }

    /**
     * Teardown
     */
    public function tearDown()
    {
        $this->clearTestTempDirectory();
    }

    protected function getTempFileName($filename)
    {
        return $this->tempdir . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Can set a filename
     */
    public function testSettingFileName()
    {
        $testFileName = $this->tempdir.'AAAAARD';
        $file = new File();
        $file->setFileName($testFileName);
        $this->assertEquals(
            $testFileName, $file->getFileName(),
            'Filename returned is not the same'
        );
    }

    /**
     * Has a simple way to create file
     */
    public function testSimpleCreateFile()
    {
        $testString = 'This is a test';
        $testFileName = $this->getTempFileName('FileTesting.txt');
        $file = File::create($testFileName);
        $file->write($testString)
             ->save();
        $this->assertFileExists($testFileName, 'Unable to create file');
        $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
    }

    /**
     * Has a long way to create a file
     */
    public function testLongWayToCreateFile()
    {
        $testString = 'This is just a string';
        $testFileName = $this->getTempFileName('GAAnotherFileToTest.txt');
        $file = new File;
        $file->setFileName($testFileName);
        $file->write($testString);
        $file->save();
        $this->assertFileExists($testFileName, 'Unable to create file');
        $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
    }

    /**
     * Can open a file and get its contents
     */
    public function testOpeningAndGettingContents()
    {
        $testString = 'Different operating system families have different line-ending conventions. When you write a text file and want to insert a line break, you need to use the correct line-ending character(s) for your operating system.';
        $testFileName = $this->getTempFileName('GAAnotherFileToTest.txt');
        file_put_contents($testFileName, $testString);
        $this->assertFileExists($testFileName, 'Unable to create test file');
        $file = new File($testFileName);
        $this->assertEquals($testString, $file->getContent());
        $this->assertEquals($testString, $file->getContents());
        $this->assertEquals($testString, $file->read());
    }

    /**
     * Has static method to unlink a file
     */
    public function testStaticUnlink()
    {
        $testFileName = $this->getTempFileName('Suchadirtyword');
        $testString = '';
        file_put_contents($testFileName, $testString);
        $this->assertFileExists($testFileName, 'Unable to create test file');
        $file = File::unlink($testFileName);
        $this->assertFileNotExists($testFileName, 'Unable to delete the file');
    }

    /**
     * Static unlink method returns false when the file does not exist
     */
    public function testStaticUnlinkReturnsFalseWhenFileDoesNotExist()
    {
        $testFileName = $this->getTempFileName('Nothingnothing');
        $this->assertFalse(
            File::unlink($testFileName),
            'File::unlink() did not return false for non-existent-file.'
        );
    }

    /**
     * Can delete a file
     */
    public function testDeleting()
    {
        $testFileName = $this->getTempFileName('Suchadirtywordaaa.txt');
        $testString = 'asdfsadf';
        file_put_contents($testFileName, $testString);
        $this->assertFileExists($testFileName, 'Unable to create test file');
        $file = new File($testFileName);
        $file->write($testString)->save();
        $file->delete();

        $this->assertFileNotExists($testFileName, 'Unable to delete the file');
    }

    /**
     * Can prepend and append contents to a file
     */
    public function testWritingBeforeAndAfter()
    {
        $testString = 'XXX';
        $testFileName = $this->getTempFileName('FileTesting.txt');
        File::create($testFileName)
                ->write($testString)
                ->writeBefore('BBBB')
                ->writeAfter('CCC')
                ->save();
        $this->assertFileExists($testFileName, 'Unable to create or save file');
        $this->assertEquals('BBBBXXXCCC', file_get_contents($testFileName), 'Unable to write successfully');
    }

    /**
     * Multiple write calls will overwrite previous content
     */
    public function testManyWritesOverwritesPreviousContent()
    {
        $testString = 'XXX';
        $testFileName = $this->getTempFileName('FileTesting.txt');
        $testfile = File::create($testFileName);
        $testfile->write($testString)->save();
        $testfile->write('iii')->save();
        $testfile->write('ABCDEFG')->save();
        $this->assertFileExists($testFileName, 'Unable to create or save file');
        $this->assertEquals('ABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
    }

    /**
     * Multiple write calls in append mode will simply append to content
     */
    public function testManyWritesButInAppendMode()
    {
        $testString = 'XXX';
        $testFileName = $this->getTempFileName('FileTesting.txt');
        $testfile = File::create($testFileName)->appendMode();
        $testfile->write($testString)->save();
        $testfile->write('iii')->save();
        $testfile->write('ABCDEFG')->save();
        $this->assertFileExists($testFileName, 'Unable to create or save file');
        $this->assertEquals('XXXiiiABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
    }

    /**
     * Creating a file that already exists raises exception
     */
    public function testRaiseExceptionWhenTheFileAlreadyExists()
    {
        $testString = 'nananananana';
        $testFileName = $this->getTempFileName('nanana.txt');
        File::create($testFileName)->write($testString)->save();
        $this->setExpectedException(
            'Asar\FileSystem\FileAlreadyExistsException',
            "File::create failed. The file '$testFileName' already exists."
        );
        File::create($testFileName);
    }

    /**
     * Can open a file using static method
     */
    public function testOpeningAFile()
    {
        $testFileName = $this->getTempFileName('nanananana.js');
        $testString = 'nananananana';
        file_put_contents($testFileName, $testString);
        $obj = File::open($testFileName);
        $this->assertTrue($obj instanceof File);
        $this->assertEquals($testString, $obj->getContent());
    }

    /**
     * Opening a file that does not exist raises exception
     */
    public function testRaiseExceptionWhenOpeningAFileThatDoesNotExist()
    {
        $testFileName = $this->getTempFileName('hahahaha.js');
        $this->setExpectedException(
            'Asar\FileSystem\FileDoesNotExistException',
            "File::open failed. The file '$testFileName' does not exist."
        );
        File::open($testFileName);
    }

    /**
     * Will raise an exception when saving a file with no file name
     */
    public function testRaiseExceptionWhenNoFileNameIsSpecified()
    {
        $file = new File;
        $this->setExpectedException(
            'Asar\FileSystem\Exception',
            "File::getResource failed. The file object does not have a file name."
        );
        $file->save();
    }

    /**
     * Raises exception for invalid filenames
     *
     * @param string $filename
     *
     * @dataProvider dataInvalidFileNames
     */
    public function testRaiseExceptionWhenSettingInvalidFileNames($filename)
    {
        $file = new File;
        $this->setExpectedException(
            'Asar\FileSystem\Exception',
            'File::setFileName failed. Filename should be a non-empty string.'
        );
        $file->setFileName($filename);
    }

    /**
     * A collection of invalid filenames used in tests
     *
     * @return array
     */
    public function dataInvalidFileNames()
    {
        return array(
            array(null),
            array(1),
            array(array(1,2,3)),
            array('')
        );
    }


    /**
     * Raises exception when creating a file on a non-existent directory
     */
    public function testRaiseExceptionWhenCreatingAFileOnANonExistentDirectory()
    {
        $this->setExpectedException(
            'Asar\FileSystem\DirectoryNotFoundException',
            'File::create failed. Unable to find the directory to create the '.
            'file to (a/non-existent/directory).'
        );
        File::create('a/non-existent/directory/file.txt');
    }

    /**
     * Can set content when using array with each element added as each line
     */
    public function testSettingContentUsingArrayAsArgument()
    {
        $content = array('AA', 'BB', 'CC', 'DD');
        $testFileName = $this->getTempFileName('temp/XXXXXXtest.txt');
        mkdir($this->getTempFileName('temp'));
        $file = File::create($testFileName)->write($content)->save();
        $this->assertEquals("AA\nBB\nCC\nDD", $file->getContent());
    }

}
