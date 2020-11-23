# Mix Helper Plugin
Helper plugin for OctoberCMS themes using Laravel Mix. Adds custom `mix()` twig function for the generated `mix-manifest.json` file.

## Setup

- Install plugin or search for `Ompmega.MixHelper`
- Enable plugin (if not automatically enabled already)
- Run `yarn dev` for development or `yarn prod` for production
- Set a custom base URL in `config/app.php`: `'mix_url' => env(MIX_ASSET_URL, null),` (optional)

## Theme structure
Below is the directory structure that the plugin uses to locate the `mix-manifest.json` file and the provided `webpack.mix.js` example uses two new directories inside of assets.

```bash
my-theme/
├── assets/
│   ├── public/                 == Public folder for generated assets
│   ├── resources/              == Resources folder for development
│   └── mix-manifest.json       == Manifest in assets directory
├── content/
├── layouts/
├── pages/
├── partials/
└── webpack.mix.js              == Webpack in theme directory
```

## Documentation

- Laravel Mix (https://laravel-mix.com)

## Issues
Please report any questions or issues [here](https://github.com/ompmega/oc-mix-helper-plugin/issues).

## License
MIT