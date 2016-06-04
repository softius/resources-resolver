<?php

namespace Softius\Resolver\Test;

use Softius\ResourcesResolver\FilenameResolver;

class FilePathResolverTest extends \PHPUnit_Framework_TestCase
{
    private $lib_dir;
    private $file_txt;

    public function setUp()
    {
        $this->lib_dir = dirname(__DIR__).DIRECTORY_SEPARATOR;
        $this->file_txt = 'tests/Resource/file.txt';
    }

    public function testResolvesFromSingleDirectory()
    {
        $resolver = new FilenameResolver($this->lib_dir);
        $filename = $resolver->resolve($this->file_txt);
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);
    }

    public function testResolvesFromMultipleDirectories()
    {
        $dirs = [
            $this->lib_dir.'src'.DIRECTORY_SEPARATOR,
            $this->lib_dir.'tests'.DIRECTORY_SEPARATOR,
        ];

        $resolver = new FilenameResolver($dirs);
        $filename = $resolver->resolve('Resource/file.txt');
        $this->assertEquals($dirs[1].'Resource/file.txt', $filename);
    }

    public function testResolvesFromIncludePath()
    {
        $include_path = get_include_path();
        set_include_path($include_path.PATH_SEPARATOR.$this->lib_dir);

        $resolver = new FilenameResolver();
        $resolver->useIncludePath(true);
        $filename = $resolver->resolve($this->file_txt);
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);

        set_include_path($include_path);
    }

    public function testResolvesExtension()
    {
        $resolver = new FilenameResolver($this->lib_dir);
        $resolver->setExtension(substr($this->file_txt, -3));

        // Trim extension from filename
        $filename = $resolver->resolve(substr($this->file_txt, 0, -4));
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);

        // Provide extension
        $filename = $resolver->resolve($this->file_txt);
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);
    }

    public function testResolvesUsingDots()
    {
        $resolver = new FilenameResolver($this->lib_dir, '.');
        $resolver->setExtension(substr($this->file_txt, -3));

        // Trim extension from filename
        $filename = $resolver->resolve(str_replace(DIRECTORY_SEPARATOR, '.', substr($this->file_txt, 0, -4)));
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);

        // Provide extension
        $filename = $resolver->resolve($this->file_txt);
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);
    }

    public function testThrowsExceptionForNotFound()
    {
        $resolver = new FilenameResolver($this->lib_dir);
        $this->setExpectedException('Exception');
        $resolver->resolve('file/does/exist.txt');
    }
}
