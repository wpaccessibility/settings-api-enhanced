# Settings API Enhanced

An improved WordPress Settings API with default render callbacks and a new accessible layout.

See the [Core Trac ticket](https://core.trac.wordpress.org/ticket/39441) for more information. This plugin is basically a direct port of the latest patches on that ticket, so don't use both the patch and the plugin at the same time. The plugin was created to make user testing (mainly of the new layout and markup) easier.

**Disclaimer:** This plugin exists for testing purposes only. It is not intended for usage in production.

## Installation

1. Download the plugin right here from GitHub.
2. Upload the unzipped directory to your `wp-content/plugins` directory.
3. Log in to WordPress and activate the plugin.

## Testing

### Testing the new layout

To test the new layout and markup, visit the Settings > General page in the admin. This is the only page that is currently modified for testing.

### Testing the enhanced Settings API

To test the Settings API infrastructure, you should use the same functions that you commonly use in WordPress Core, prefixed with `sae_`. Note that this prefix only exists to prevent collisions with duplicate function names; in the final implementation the new functionality will be merged into the original functions. The plugin also introduces several new functions. These are not prefixed and will use their names as-is when they get merged into Core.

The following functions replace similar Core functions:

* `sae_add_settings_section()`
* `sae_add_settings_field()`
* `sae_do_settings_sections()`
* `sae_do_settings_fields()`

The functions are fully backward-compatible with how they currently work in WordPress Core. Please have a look at the inline documentation and code for instructions on how to make use of the enhancements.

## Goals

The primary goal of these efforts is to make the content produced by the Settings API more accessible by getting rid of the form tables and introducing default accessible callbacks for common fields. Once the markup, layout and styles are final, the idea is to also bring them over to similar areas in the admin.
