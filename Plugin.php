<?php

namespace Ompmega\MixHelper;

use System\Classes\PluginBase;
use Cms\Classes\Theme;
use Cms\Classes\Asset;
use Cms;
use Cache;
use Carbon\Carbon;
use Config;
use October\Rain\Exception\SystemException;

/**
 * Class Plugin
 *
 * @package Ompmega\MixHelper
 */
class Plugin extends PluginBase
{
    private $theme;

    private $manifest;

    /**
     * {@inheritdoc}
     */
    public function pluginDetails(): array
    {
        return [
            'name'          => 'Mix Helper',
            'description'   => 'Helper plugin for themes using Laravel Mix. Adds custom `mix()` twig function for the generated mix-manifest.json.',
            'author'        => 'Ompmega',
            'icon'          => 'oc-icon-puzzle-piece',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerMarkupTags(): array
    {
        return [
            'functions' => [
                'mix' => [$this, 'readMixManifest']
            ]
        ];
    }

    /**
     * Locate contents of generated manifest file.
     *
     * @param string $assetFilePath
     * @return string
     * @throws SystemException
     */
    public function readMixManifest(string $path): string
    {
        $theme = $this->theme = Theme::getActiveTheme();
        $manifestCacheKey = sprintf('%s:%s', $theme->getDirName(), 'mix-manifest' );

        // Skips caching when debug mode enabled
        if (Config::get('app.debug')) {
            $manifest = $this->getManifest();
        } else {
            $manifest = Cache::get($manifestCacheKey, function () use ($theme, $manifestCacheKey) {

                $manifest = $this->getManifest();

                Cache::add(
                    $manifestCacheKey,
                    $manifest,
                    Carbon::now()->addHour()
                );

                return $manifest;
            });
        }

        if (!isset($manifest[$path])) {
            throw new SystemException("Unable to locate Mix file: {$path}.");
        }

        return Cms::url(
            sprintf('/themes/%s/assets', $theme->getDirName()) . $manifest[$path]
        );
    }

    /**
     * Loads the manifest contents and parses to JSON.
     *
     * @return array
     * @throws SystemException
     */
    protected function getManifest(): array
    {
        $asset = Asset::load($this->theme, 'mix-manifest.json');

        if (!$asset || is_null($asset)) {
            throw new SystemException("The Mix manifest does not exist.");
        }

        return json_decode($asset->content, true);
    }
}
