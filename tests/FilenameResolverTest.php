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

    public function testResolversFromIncludePath()
    {
        $include_path = get_include_path();
        set_include_path($include_path.PATH_SEPARATOR.$this->lib_dir);

        $resolver = new FilenameResolver();
        $resolver->useIncludePath(true);
        $filename = $resolver->resolve($this->file_txt);
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);

        set_include_path($include_path);
    }

    public function testResolvesWithoutExtension()
    {
        $resolver = new FilenameResolver($this->lib_dir);
        $resolver->setExtension(substr($this->file_txt, -3));
        $filename = $resolver->resolve(substr($this->file_txt, 0, -4));
        $this->assertEquals($this->lib_dir.$this->file_txt, $filename);
    }

    public function testThrowsExceptionForNotFound()
    {
        $resolver = new FilenameResolver($this->lib_dir);
        $this->setExpectedException('Exception');
        $resolver->resolve('file/does/exist.txt');
    }
}
