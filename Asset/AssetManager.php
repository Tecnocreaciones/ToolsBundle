<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Asset;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Cache\PhpFileCache;

class AssetManager
{
    const CACHE_MODES_NAME = 'solution.code.mirror.modes';
    const CACHE_THEMES_NAME = 'solution.code.mirror.themes';

    /** @var  FileLocator */
    protected $fileLocator;

    protected $modes = array();
    protected $extraModes = array();
    protected $addons = array();
    protected $addonsParsed = null;

    protected $themes = array();

    protected $modeDirs = array();

    protected $themesDirs = array();

    protected $cacheDriver;

    protected $env;
    
    protected $codemirrorLib;
    
    function __construct($fileLocator, array $parameters,$codemirrorLib, $cacheDir, $env)
    {
        $this->fileLocator = $fileLocator;
        $this->modeDirs = $parameters["mode_dirs"];
        $this->themesDirs = $parameters["themes_dirs"];
        $this->addons = $parameters["addons"];
        $this->extraModes = $parameters["modes"];
        $this->cacheDriver = new PhpFileCache($cacheDir);
        $this->env = $env;
        if($codemirrorLib === null){
            $codemirrorLib = "/bundles/tecnocreacionestools/codemirror/js/codemirror.js";
        }
        $this->codemirrorLib = $codemirrorLib;
        #check env and fetch cache
        if ($this->env == 'prod' && $cacheModes = $this->cacheDriver->fetch(static::CACHE_MODES_NAME)) {
            $this->modes = $cacheModes;
        } else {
            $this->parseModes();
        }

        if ($this->env == 'prod' && $cacheThemes = $this->cacheDriver->fetch(static::CACHE_THEMES_NAME)) {
            $this->themes = $cacheThemes;
        } else {
            $this->parseThemes();
        }
    }

    public function addMode($key, $resource)
    {
        $this->modes[$key] = $resource;

        return $this;
    }
    
    public function getExtraModes() {
        $modes = [];
        foreach ($this->extraModes as $extraMode) {
            $m = $this->getMode($extraMode);
            if($m !== false){
                $modes[]=$m;
            }
        }
        return $modes;
    }
    
    public function getMode($key)
    {
        return isset( $this->modes[$key]) ? $this->modes[$key] : false;
    }

    public function addTheme($key, $resource)
    {
        $this->themes[$key] = $resource;

        return $this;
    }

    public function getTheme($key)
    {
        return isset( $this->themes[$key]) ? $this->themes[$key] : false;
    }

    public function getModes()
    {
        return $this->modes;
    }
    function getAddonsParsed() {
        if($this->addonsParsed === null){
            $this->addonsParsed = [];
            foreach ($this->addons as $addon) {
                $this->addonsParsed[] = $this->parseDir($addon);
            }
        }
        return $this->addonsParsed;
    }

        public function getThemes()
    {
        return $this->themes;
    }
    
    public function getCodemirrorLib() {
        return $this->codemirrorLib;
    }

    /**
     * Parse editor mode from dir
     */
    protected function parseModes()
    {
        foreach ($this->modeDirs as $dir) {
            $absDir = $this->fileLocator->locate($dir);

            $finder = Finder::create()->files()->in($absDir)->notName("*test.js")->name('*.js');
            
            foreach ($finder as $file) {
                $this->addModesFromFile($dir,$file);
            }
        }
        #save to cache if env prod
        if ($this->env == 'prod') {
            $this->cacheDriver->save(static::CACHE_MODES_NAME, $this->getModes());
        }
    }
    
    private function parseDir($dir) {
        $dir = str_replace("@","",$dir);
        $dir = str_replace("/Resources/public","",$dir);
        $dir = str_replace("Bundle","",$dir);
        $dir = strtolower($dir);
        $dir = "bundles/".$dir;
        return $dir;
    }


    /**
     * Parse editor modes from dir
     */
    protected function addModesFromFile($dir,$file)
    {
        $dir = $this->parseDir($dir);

        $jsContent = $file->getContents();
        preg_match_all('#defineMIME\(\s*(\'|")([^\'"]+)(\'|")#', $jsContent, $modes);
        if (count($modes[2])) {
            foreach ($modes[2] as $mode) {
                $this->addMode($mode, $dir."/".$file->getRelativePathname());
            }
        }
        $this->addMode($file->getRelativePath(), $dir."/".$file->getRelativePathname());

                #save to cache if env prod
        if ($this->env == 'prod') {
            $this->cacheDriver->save(static::CACHE_MODES_NAME, $this->getThemes());
        }
    }
    /**
     * Parse editor themes from dir
     */
    protected function parseThemes()
    {
        foreach ($this->themesDirs as $dir) {
            $absDir = $this->fileLocator->locate($dir);
            $finder = Finder::create()->files()->in($absDir)->name('*.css');
            foreach ($finder as $file) {
                $this->addTheme($file->getBasename('.css'), $file->getPathname());
            }
        }
        #save to cache if env prod
        if ($this->env == 'prod') {
            $this->cacheDriver->save(static::CACHE_THEMES_NAME, $this->getThemes());
        }
    }
}

