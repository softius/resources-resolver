<?php

namespace Softius\ResourcesResolver;

class FilenameResolver implements ResolvableInterface
{
    const DEFAULT_DIRECTORY_SEPARATOR = DIRECTORY_SEPARATOR;

    /**
     * @var bool
     */
    private $directory_separator;

    /**
     * @var array
     */
    private $dirs;

    /**
     * @var bool
     */
    private $use_include_path;

    /**
     * @var string
     */
    private $extension;

    /**
     * FilePathResolver constructor.
     *
     * @param null $directories
     * @param null $directory_separator
     */
    public function __construct($directories = null, $directory_separator = null)
    {
        $this->directory_separator = ($directory_separator === null) ? self::DEFAULT_DIRECTORY_SEPARATOR : $directory_separator;

        $this->dirs = [];
        if (is_string($directories)) {
            $this->addDirectory($directories);
        } elseif (is_array($directories)) {
            $this->addDirectories($directories);
        }

        $this->useIncludePath(false);
    }

    /**
     * @param array $directories
     */
    public function addDirectories($directories)
    {
        $this->dirs = array_merge($this->dirs, $directories);
    }

    /**
     * @param string $directory
     */
    public function addDirectory($directory)
    {
        array_push($this->dirs, $directory);
    }

    /**
     * @param bool $use
     */
    public function useIncludePath($use)
    {
        $this->use_include_path = $use;
    }

    /**
     * @param $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @param string $in
     * @return null|string
     * @throws \Exception
     */
    public function resolve($in)
    {
        $partial_path = $in;

        if ($this->directory_separator !== self::DEFAULT_DIRECTORY_SEPARATOR) {
            $dir_name = pathinfo($partial_path, PATHINFO_DIRNAME);
            $partial_path = str_replace($dir_name, str_replace($this->directory_separator, self::DEFAULT_DIRECTORY_SEPARATOR, $dir_name), $partial_path);
        }

        if (pathinfo($partial_path, PATHINFO_EXTENSION) == '' && !empty($this->extension)) {
            $partial_path = $partial_path.'.'.$this->extension;
        }

        $dirs = $this->dirs;
        if ($this->use_include_path) {
            $dirs = array_merge($dirs, explode(PATH_SEPARATOR, get_include_path()));
        }

        foreach ($dirs as $dir) {
            $path = $dir.DIRECTORY_SEPARATOR.$partial_path;
            if (file_exists($path)) {
                return realpath($path);
            }
        }

        throw new \Exception(sprintf('Could not resolve %s to a filename', $in));
    }
}
