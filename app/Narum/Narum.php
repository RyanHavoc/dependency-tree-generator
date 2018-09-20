<?php

namespace App\Narum;

use App;
use Config;

class Narum
{
    public $env;
    public $host;
    public $paths;
    public $root;
    public $public_dir;
    public $assets_dir;
    public $manifest_path;
    public $partials_dir;

    public function __construct()
    {
        $protocol = getenv('APP_SSL') == 'true' ? 'https://' : 'http://';

        $this->env = getenv('APP_ENV') ? getenv('APP_ENV') : App::environment();
        $this->host = $protocol.getenv('APP_DOMAIN');
        $this->root = substr(App::publicPath(), 0, strpos(App::publicPath().'/', '/public/'));
        $this->paths = Config::get($this->env.'.paths');

        $this->assets_dir = '/assets/';
        $this->public_dir = App::publicPath();
        $this->manifest_path = $this->public_dir.'/assets/rev-manifest.json';
    }

    /**
     * Set robot crawling. Disabled for anything but production.
     */
    public function setRobots()
    {
        if ($this->env == 'production') {
            echo '<meta name="robots" content="robots.txt" />';
        } else {
            echo '<meta name="robots" content="noindex, nofollow" />';
        }
    }

    /**
     * Set GA code. Disabled for anything but production.
     */
    public function setGA($trackingCode)
    {
        if ($this->env == 'production') {
            echo "
            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', '{$trackingCode}', 'auto');
              ga('send', 'pageview');
            </script>
            ";
        }
    }

    /**
     * Get asset fingerprint path.
     * @param  string $filename
     * @return string
     */
    public function getAssetPath($filename)
    {
        if (file_exists($this->manifest_path)) {
            $manifest = json_decode(file_get_contents($this->manifest_path), true);
        } else {
            $manifest = [];
        }

        if (array_key_exists($filename, $manifest)) {
            return $this->assets_dir.$manifest[$filename];
        }

        return $this->assets_dir.$filename;
    }

    /**
     * Load asset helper.
     * @param  string $type
     * @param  string $support standard|ie
     * @param  string $location head|foot
     * @return null
     */
    public function loadAsset($type, $support = 'standard', $location = 'foot')
    {
        if (array_key_exists($type, $this->paths[$support])) {
            if ($type == 'css') {
                $this->loadCSS($this->paths[$support][$type]);
            }

            if ($type == 'js') {
                $this->loadJS($this->paths[$support][$type][$location]);
            }
        }
    }

    /**
     * Load CSS assets.
     * @param  string|array $asset
     * @return null
     */
    private function loadCSS($asset)
    {
        if (is_array($asset)) {
            foreach ($asset as $item) {
                if ($this->assetExists($item)) {
                    echo $this->linkStylesheet($item);
                }
            }
        } else {
            if ($this->assetExists($asset)) {
                echo $this->linkStylesheet($asset);
            }
        }
    }

    /**
     * Load JS assets.
     * @param  string|array $asset
     * @return null
     */
    private function loadJS($asset)
    {
        if (is_array($asset)) {
            foreach ($asset as $item) {
                if ($this->assetExists($item)) {
                    echo $this->referenceScript($item);
                }
            }
        } else {
            if ($this->assetExists($asset)) {
                echo $this->referenceScript($asset);
            }
        }
    }

    /**
     * Determine if an asset physically exists.
     * @param  string $asset
     * @return bool
     */
    private function assetExists($asset)
    {
        if (file_exists($this->public_dir.'/assets/'.$asset)) {
            return true;
        }

        return false;
    }

    /**
     * Format stylesheet link.
     * @param  string $href
     * @return string
     */
    private function linkStylesheet($href)
    {
        $path = $this->getAssetPath($href);

        return "<link rel=\"stylesheet\" href=\"{$path}\" />";
    }

    /**
     * Format script reference.
     * @param  string $src
     * @return string
     */
    private function referenceScript($src)
    {
        $path = $this->getAssetPath($src);

        return "<script src=\"{$path}\"></script>";
    }
}
