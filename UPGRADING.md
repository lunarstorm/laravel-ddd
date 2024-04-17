# Upgrading
 
 ## From 0.x to 1.x
- Minimum required Laravel version is 10.25.
- The ddd generator [command syntax](README.md#usage) in 1.x. Generator commands no longer receive a domain argument. For example, instead of `ddd:action Invoicing CreateInvoice`, one of the following would be used:
    - Using the --domain option: ddd:action CreateInvoice --domain=Invoicing (this takes precedence).
    - Shorthand syntax: ddd:action Invoicing:CreateInvoice.
    - Or simply ddd:action CreateInvoice to be prompted for the domain afterwards.
- The [config file](config/ddd.php) was refactored. A helper command `ddd:upgrade` is available to assist with this, but it is strongly recommended that you simply wipe out the old config and re-publish via `php artisan vendor:publish --tag="ddd-config"` and re-configure accordingly.
- If applicable, stubs should also be re-published via `php artisan vendor:publish --tag="ddd-stubs"` and re-customized as needed.
- In production, `ddd:cache` should be run during the deployment process. See the [Autoloading in Production](README.md#autoloading-in-production) section for more details.
