=== Better Share Buttons ===
Contributors:		kubiq
Donate link:		https://www.paypal.me/jakubnovaksl
Tags:				share, icons, social media, sharing, buttons, marketing, links, email, linkedin, reddit, facebook, twitter, pinterest, whatsapp, instagram, youtube
Tested up to:		6.6
Requires at least:	5.7
Requires PHP:		7.0
Stable tag:			1.9.2
License:			GPLv2 or later
License URI:		http://www.gnu.org/licenses/gpl-2.0.html

Easily use [better_share_buttons] shortcode anywhere you want.

== Description ==

Easily use `[better_share_buttons]` shortcode anywhere you want.
Create your own styles, or choose from existing ones.
This plugin is optimized for fastest websites - it will not load any JS files and it will load CSS file only when it is needed.

**This plugin allows you to:**

* create multiple social share icon styles
* use shortcode to display share icons anywhere you want
* create your own styles in your theme folder
* customize basic settings directly in admin
* use different icons for social networks

## Hooks

**customize wrapper classes**

`add_filter( 'share_buttons_classes', function( $classes, $share_buttons_settings, $share_buttons_id ){
	$classes[] = 'my-custom-class';
	return $classes;
}, 10, 3 );`

&nbsp;

**customize wrapper CSS output**

`add_filter( 'share_buttons_styles', function( $style, $share_buttons_settings, $share_buttons_id ){
	$style[] = '--my-var: 50px';
	return $style;
}, 10, 3 );`

&nbsp;

**customize html form more button**

`add_filter( 'better_share_buttons_more', function( $html, $share_buttons_settings, $share_buttons_id ){
	// replace SVG icon path with a custom one
	$html = str_replace( 'M18 14V8h-4v6H8v4h6v6h4v-6h6v-4h-6z', 'M18.2 4.2C18.2 3 17.2 2 16 2s-2.2 1-2.2 2.2v9.7H4.2C3 13.8 2 14.8 2 16s1 2.2 2.2 2.2h9.7v9.7c0 1.2 1 2.2 2.2 2.2s2.2-1 2.2-2.2v-9.7H28c1.2 0 2.2-1 2.2-2.2s-1-2.2-2.2-2.2h-9.7V4.2z', $html );
	return $html;
}, 10, 3 );`

&nbsp;

**add custom icon for any network**

`add_filter( 'share_buttons_icons', function( $icon, $slug, $share_buttons_settings, $share_buttons_id ){
	if( $slug == 'facebook' ){
		// add custom SVG path and you will be able to select it in admin
		$icon[] = 'M29.3 16c0-7.4-6-13.3-13.3-13.3C8.6 2.7 2.7 8.6 2.7 16c0 6.5 4.6 11.8 10.7 13.1V20h-2.7v-4h2.7v-3.3c0-2.6 2.1-4.7 4.7-4.7h3.3v4h-2.7c-.7 0-1.3.6-1.3 1.3V16h4v4h-4v9.3c6.7-.7 11.9-6.4 11.9-13.3z';
	}
	return $icon;
}, 10, 4 );`

&nbsp;

**control shared URL**

`add_filter( 'better_share_buttons_permalink', function( $url ){
	// ...
	return $url;
});`

&nbsp;

**control shared title**

`add_filter( 'better_share_buttons_title', function( $title ){
	// ...
	return $title;
});`

&nbsp;

**control HTML output for any network**

`add_filter( 'better_share_buttons_link', function( $html, $slug, $share_buttons_settings, $share_buttons_id ){
	if( $slug == 'facebook' ){
		$html = str_replace( 'https://facebook.com/sharer/sharer.php?', 'https://facebook.com/sharer/sharer.php?xxx=yyy&', $html );
	}
	return $html;
}, 10, 4 );`

&nbsp;

## Create your own styles

Create new folder `better-share-buttons` inside your theme folder and put iniside style files `xyz.css` and `xyz.json`.

Style `xyz` will automatically appear in the admin settings.

Content of JSON file is used to manipulate settings in admin when you select that style:

`{
	"visible_buttons": 999,
	"bsb_mx": 0,
	"bsb_my": 0,
	"bsb_px": 5,
	"bsb_py": 5,
	"bsb_br": 0,
	"bsb_fz": 0,
	"bsb_iz": 32,
	"bsb_lls": 0,
	"bsb_lrs": 0
}`

Content of CSS file should define your style:

`.better_share_buttons.bsb-style-xyz{}`


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Changelog ==

= 1.9.2 =
* new icon for WhatsApp
* fix icon size for facebook
* commented dev code to list all icons in admin

= 1.9.1 =
* tested on WP 6.6
* fix missing filter for custom icons in admin

= 1.9 =
* tested on WP 6.5
* fix whatsapp link broken by esc_url

= 1.8.1 =
* new icon for copy link

= 1.8 =
* tested on WP 6.4

= 1.7 =
* new unicolor-icons style

= 1.6 =
* new icon for email

= 1.5 =
* new unicolor-list style

= 1.4 =
* new inline-squares style
* new twitter icon
* new copy link icon
* new data-network link attribute for easier CSS targeting

= 1.3 =
* added filters for shared URL and shared TITLE

= 1.2 =
* new icon for copy link
* new unicolor-hover style

= 1.1 =
* optimize settings page
* fix floating style jump

= 1.0 =
* Release