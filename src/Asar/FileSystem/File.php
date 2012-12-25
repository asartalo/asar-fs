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

use \Asar\FileSystem\Exception as FileException;

/**
 * A wrapper class for simplifying file creation and access
 *
 * EXAMPLE - File Creation
 * The following code creates a file named 'filename.ext'
 * with the content 'Hello World!' and saves it.
 *
 * <code>
 *   Asar\FileSystem\File::create('filename.ext')->write('Hello World!')->save();
 * </code>
 *
 * The following code creates a file named 'filename.ext'
 * in the 'path/' directory ('this directory must exist'),
 * writes the content 'Hello Again!' and saves it.
 *
 * <code>
 *   Asar\File::create('path/filename.ext')->write('Hello Again!')->save();
 * </code>
 *
 *
 * EXAMPLE - Opening a File
 *
 * The following gets the contents of a file:
 *
 * <code>
 *   $contents = Asar\FileSystem\File::open('thefile.ext')->read();
 * </code>
 *
 * The following opens a file, writes a content on it, and then saves it:
 *
 * <code>
 *   $f = Asar\File::open('thefile.ext')->write($thecontentstring)->save();
 * </code>
 *
 *
 * The static methods {@link open()} and {@link create()} are wrappers for the
 * constructor method
 *
 *
 * Created on Jul 2, 2007
 *
 * @todo       Changing File Mode (chmod?)
 * @todo       Making sure we point to the right file
 */
class File
{

    private $filename;

    private $content;

    private $resource;

    private $mode = 'a+b';

    private $forcedAppendMode = false;

    /**
     * Creates a file
     *
     * @param string $filename
     *
     * @return File
     */
    public static function create($filename)
    {
        if (file_exists($filename)) {
            throw new FileAlreadyExistsException(
                "Asar\File::create failed. The file '$filename' already exists."
            );
        }
        if (!file_exists(dirname($filename))) {
            throw new DirectoryNotFoundException(
                'Asar\File::create failed. Unable to find the directory ' .
                'to create the file to (' . dirname($filename) . ').'
            );
        }

        return new self($filename, 'w+b');
    }

    /**
     * Opens a file
     *
     * @param string $filename
     *
     * @return File
     */
    public static function open($filename)
    {
        if (!file_exists((string) $filename)) {
            throw new FileDoesNotExistException(
                "Asar\File::open failed. The file '$filename' does not exist."
            );
        } else {
            return new self($filename, 'r+b');
        }
    }

    /**
     * Unlinks (deletes) a file
     *
     * @param string $filename
     *
     * @return boolean wether the file has been successfuly unlinked
     */
    public static function unlink($filename)
    {
        if (file_exists((string) $filename)) {
            return unlink($filename);
        } else {
            return false;
        }
    }

    /**
     * Constructor
     *
     * @param string $filename the name of the file
     * @param string $mode     the mode used to open file
     */
    public function __construct($filename = null, $mode = 'a+b')
    {
        if (is_string($filename)) {
            $this->setFileName($filename);
            $this->mode = $mode;
            if (file_exists($this->getFileName())) {
                $this->content = file_get_contents($this->getFileName());
            }
        }
    }

    /**
     * Sets the file writing to append mode
     *
     * @return File the file object ($this)
     */
    public function appendMode()
    {
        $this->mode = 'a+b';
        $this->unsetResource();
        $this->getResource();
        $this->forcedAppendMode = true;

        return $this;
    }

    /**
     * Returns the resource used for the file
     *
     * @return resource a file resource
     */
    private function getResource()
    {
        if (!is_resource($this->resource)) {
            // Attempt to create a resource using filename
            if (!$this->getFileName()) {
                throw new FileException(
                    'Asar\File::getResource failed. The file object ' .
                    'does not have a file name.'
                );
            }
            $this->resource = fopen($this->filename, $this->mode);
        }

        return $this->resource;
    }

    private function unsetResource()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    /**
     * Sets the filename
     *
     * @param string $filename the file's name
     */
    public function setFileName($filename)
    {
        if (!is_string($filename) || $filename === '') {
            throw new FileException(
                'Asar\File::setFileName failed. Filename should be a non-empty string.'
            );
        }
        $this->filename = $filename;
    }

    /**
     * Returns the file's filename
     *
     * @return string the filename
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * Sets the file's contents
     *
     * @param string $content the file contents
     */
    public function setContent($content)
    {
        if (is_array($content)) {
            $content = implode("\n", $content);
        }
        $this->content = (string) $content;
    }

    /**
     * Returns the file's contents
     *
     * @return string the contents of the file
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Alias to getContent()
     *
     * @return string the contents of the file
     */
    public function getContents()
    {
        return $this->getContent();
    }

    /**
     * Writes the content of the file and returns self
     *
     * Useful for chaining calls
     *
     * @param string $content the contents to write to file
     *
     * @return File the file object ($this)
     */
    public function write($content)
    {
        $this->setContent($content);

        return $this;
    }

    /**
     * Prepends some content to the file
     *
     * @param string $content the content to prepend
     *
     * @return File the file object ($this)
     */
    public function writeBefore($content)
    {
        return $this->write($content.$this->getContent());
    }

    /**
     * Appends some content to the file
     *
     * @param string $content the content to append
     *
     * @return File the file object ($this)
     *
     * @todo Optimize for append_mode
     */
    public function writeAfter($content)
    {
        return $this->write($this->getContent().$content);
    }


    /**
     * Alias to getContent()
     *
     * @return string the file's contents
     */
    public function read()
    {
        return $this->getContent();
    }

    /**
     * Save the file
     *
     * @return File the file object ($this)
     */
    public function save()
    {
        fwrite($this->getResource(), $this->getContent());
        if (!$this->forcedAppendMode) {
            $this->unsetResource();
        }

        return $this;
    }

    /**
     * Deletes a file
     *
     * @return boolean whether the file has been successfuly deleted
     */
    public function delete()
    {
        $this->unsetResource();

        return unlink($this->getFileName());
    }

    /**
     * Destructor
     *
     * Unsets the resource
     */
    public function __destruct()
    {
        $this->unsetResource();
    }
}

