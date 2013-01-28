=== nginx Compatibility ===
Contributors: vladimir_kolesnikov
Donate link: http://blog.sjinks.org.ua/
Tags: nginx, pretty permalinks, FastCGI
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: 0.2

The plugin makes WordPress more friendly to nginx.

== Description ==

The plugin solves two problems:

1. When WordPress detects that FastCGI PHP SAPI is in use, it
[disregards the redirect status code](http://blog.sjinks.org.ua/wordpress/510-wordpress-fastcgi-and-301-redirect/)
passed to `wp_redirect`. Thus, all 301 redrects become 302 redirects
which may not be good for SEO. The plugin overrides `wp_redirect` when it detects
that nginx is used.
1. When WordPress detects that `mod_rewrite` is not loaded (which is the case for nginx as
it does not load any Apache modules) it falls back to [PATHINFO permalinks](http://codex.wordpress.org/Using_Permalinks#PATHINFO:_.22Almost_Pretty.22)
in Permalink Settings page. nginx itself has built-in support for URL rewriting and does not need
PATHINFO permalinks. Thus, when the plugin detects that nginx is used, it makes WordPress think
that `mod_rewrite` is loaded and it is OK to use pretty permalinks.

The plugin does not require any configuration. It just does its work.
You won't notice it — install and forget.

== Installation ==

1. Upload `nginx-compatibility` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress. The plugins comes in two flavors: PHP4 (experimental) and PHP5. Please activate
the flavor that matches your PHP version.
1. That's all :-)

== Frequently Asked Questions ==

None yet. Be the first to ask.
