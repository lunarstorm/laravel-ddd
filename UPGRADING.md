# Upgrading

## From 1.1.x to 1.2.x
### Breaking
- Stubs are now published `to base_path('stubs/ddd')` instead of `resource_path('stubs/ddd')`. In other words, they are now co-located alongside the framework's published stubs, within a ddd subfolder.
- Published stubs now use `.stub` extension instead of `.php.stub` (following Laravel's convention).
- If you haven't previously published stubs in your installation, this change will not affect you.

### Update Config
- Support for Application Layer and Custom Layers was added, introducing changes to the config file.
- Run `php artisan ddd:config update` to rebuild your application's published `ddd.php` config to align with the package's latest copy.
- The update utility will attempt to respect your existing customizations, but you should still review and verify manually.

### Publishing Stubs
- Old way (removed): `php artisan vendor:publish --tag="ddd-stubs"`
- New way, using stub utility command: `php artisan ddd:stub` (see [Customizing Stubs](README.md#customizing-stubs) in README for more details).

## From 0.x to 1.x
- Minimum required Laravel version is 10.25.
- The ddd generator [command syntax](README.md#usage) in 1.x. Generator commands no longer receive a domain argument. For example, instead of `ddd:action Invoicing CreateInvoice`, one of the following would be used:
    - Using the --domain option: ddd:action CreateInvoice --domain=Invoicing (this takes precedence).
    - Shorthand syntax: ddd:action Invoicing:CreateInvoice.
    - Or simply ddd:action CreateInvoice to be prompted for the domain afterwards.
- The [config file](config/ddd.php) was refactored. A helper command `ddd:upgrade` is available to assist with this, but it is strongly recommended that you simply wipe out the old config and re-publish via `php artisan vendor:publish --tag="ddd-config"` and re-configure accordingly.
- If applicable, stubs should also be re-published via `php artisan vendor:publish --tag="ddd-stubs"` and re-customized as needed.
- In production, `ddd:cache` should be run during the deployment process. See the [Autoloading in Production](README.md#autoloading-in-production) section for more details.
