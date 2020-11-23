<?php

namespace Ompmega\MixHelper;

use System\Classes\PluginBase;
use October\Rain\Exception\SystemException;
use Cms\Classes\Theme;
use Cms\Classes\Asset;
use Carbon\Carbon;
use Config;
use Cache;
use Cms;

/**
 * Class Plugin
 *
 * @package Ompmega\MixHelper
 */
class Plugin extends PluginBase
{
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
     * @param string $path
     * @return string
     * @throws SystemException
     */
    public function readMixManifest(string $path): string
    {
        $theme = Theme::getActiveTheme();
        $manifestCacheKey = sprintf('%s:%s', $theme->getDirName(), 'mix-manifest' );

        // Skips caching when debug mode enabled
        if (Config::get('app.debug')) {
            $manifest = $this->getManifest($theme);
        }
        else {
            $manifest = Cache::get($manifestCacheKey, function () use ($theme, $manifestCacheKey) {

                $manifest = $this->getManifest($theme);

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

        $customBaseUrl = Config::get('app.mix_url');

        if ($customBaseUrl) {
            return $customBaseUrl . $manifest[$path];
        }

        return sprintf('/themes/%s/assets', $theme->getDirName()) . $manifest[$path];
    }

    /**
     * Loads the manifest contents and parses to JSON.
     *
     * @param Cms\Classes\Theme $theme
     * @return array
     * @throws SystemException
     */
    protected function getManifest($theme): array
    {
        $asset = Asset::load($theme, 'mix-manifest.json');

        if (!$asset || is_null($asset)) {
            throw new SystemException("The Mix manifest does not exist.");
        }

        return json_decode($asset->content, true);
    }
}
