=== commonWP ===
Contributors: dimadin
Donate link: https://milandinic.com/donate/
Tags: CDN, Free CDN, Public CDN, jsDelivr, performance
Requires at least: 4.7
Tested up to: 5.1
Requires PHP: 5.4
Stable tag: 1.1.0

Offload open source static assets to the free, public CDN.

== Description ==

commonWP is a plugin that enables usage of free, public CDN ([jsDelivr](https://www.jsdelivr.com/)) for open source JavaScript and CSS files. Those files are:

* All files from WordPress core, unless development version of WordPress is used.
* All files from plugins hosted by WordPress.org Plugins Repository, unless author of specific plugin doesn't use [SVN tags for releasing](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/#tagging-new-versions).
* All files from themes hosted by WordPress.org Themes Repository.
* All files from plugins and themes hosted on [GitHub](https://github.com/) that support [GitHub Updater](https://github.com/afragen/github-updater).
* All files marked as available on [npm](https://www.npmjs.com/) in any type of theme, plugin, or MU plugin.

It aims to be both lightweight and very secure. The approach used in this plugin is safer then in any other plugin that enables usage of any CDN. First, commonWP will only rewrite file to point to one on jsDelivr if that remote file is identical to local one. Second, during comparison, it generates [subresource identity hash](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity) of remote jsDelivr file and includes that hash in page's source code so browser won't load remote file if it doesn't have exactly the same hash.

There are no any settings. After activation, it will fill up its cache in the background, and that may take some time depending on number of resources site uses.

For much more details about commonWP, head over to its [GitHub repository](https://github.com/dimadin/commonWP).

== Changelog ==

= 1.1.0 =
* Released on 27th December 2018
* Define scripts added in WordPress 5.0 that are available on npm.
* Fix version of WordPress core on GitHub for major versions.
* Disable Jetpack Site Accelerator for static files.
* Minor inline documentation fixes.
