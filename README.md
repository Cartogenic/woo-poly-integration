# [WooPoly](https://github.com/decarvalho/woopoly/)

** Update 18/08/2016: ** Hyyan has resume development of his plugin. My focus from now on is to port the but fixes
to his plugin version, so that they can be made accessible to all users via Wordpress reporsitory. Therefore,
** _I don't plan to continue to develop further this fork. The 1.0.2 is the last release._ **

This Wordpress plugin makes it possible to run multilingual e-commerce sites using WooCommerce and Polylang.

This is a fork of the [Hyyan WooCommerce Polylang Integration](https://wordpress.org/plugins/woo-poly-integration/)
develop by Hyyan that is no longer mantained. Credit goes entirely
to Hyyan for this great plugin.

## Features

- [x] Auto Download Woocommerce Translation Files
- [x] Page Translation
- [x] Endpoints Translation
- [x] Product Translation
  - [x] Categories
  - [x] Tags
  - [x] Attributes
  - [x] Shipping Classes
  - [x] Meta Synchronization
  - [x] Variation Product
  - [x] Product Gallery
- [x] Order Translation
- [x] Stock Synchronization
- [x] Cart Synchronization
- [x] Coupon Synchronization
- [x] Emails
- [x] Reports
  - [x] Filter by language
  - [x] Combine reports for all languages


## What you need to know about this plugin

1. The plugin needs `PHP5.3 and above`
2. This plugin is developed in sync with [Polylang](https://wordpress.org/plugins/polylang)
   and [WooCommerce](https://wordpress.org/plugins/woocommerce/) latest version.
3. The plugin support variable products, but using them will `disallow you to
   change the default language`, because of the way the plugin implements this
   support. So you have to make sure to choose the default language before you start
   adding new variable products.
4. Polylang URL modifications method `The language is set from content` is not
   supported yet

## How to install

1. Download the plugin as zip archive and then upload it to your wordpress plugins folder and
extract it there.
2. Activate the plugin from your admin panel

## Setup your environment

1. You need to translate woocommerce pages by yourself
2. The plugin will handle the rest for you

## Translations

* Arabic by [Hyyan Abo Fakher](https://github.com/hyyan)

## Contributing

Everyone is welcome to help contribute and improve this plugin. There are several
ways you can contribute:

* Reporting issues (please read [issue guidelines](https://github.com/necolas/issue-guidelines))
and report the issues [here](https://github.com/decarvalhoaa/woopoly/issues)
* Suggesting new features [here](https://github.com/decarvalhoaa/woopoly/issues)
* Fixing issue (fork from the [master](https://github.com/decarvalhoaa/woopoly) and make a pull request with your fixes)
