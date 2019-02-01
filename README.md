# Mix Helper Plugin
Helper plugin for OctoberCMS themes using Laravel Mix. Adds custom `mix()` twig function for the generated `mix-manifest.json` file.

## Theme structure
Below is the directory structure that the plugin uses to locate the `mix-manifest.json` file:

```bash
    my-theme/
    ├── assets/
    │   ├── public/
    │   ├── resources/
    │   └── mix-manifest.json       <== Manifest in assets directory
    ├── content/
    ├── layouts/
    ├── pages/
    ├── partials/
    └── webpack.mix.js              <= Webpack in theme directory
```

## License
MIT