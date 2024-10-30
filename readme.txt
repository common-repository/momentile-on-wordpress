=== Momentile on WordPress ===
Contributors: chrismou
Donate link: http://mou.me.uk/projects/wordpress/plugins/momentile-on-wordpress/
Tags: momentile, widget, admin, sidebar
Requires at least: 2.8
Tested up to: 3.0.4
Stable tag: 0.6.2

Display 1 or more of your latest Momentile photos (http://momentile.com) on your blog sidebar.

== Description ==

This plugin allows you to display 1 or more of your latest Momentile photos (http://momentile.com) on your blog sidebar.  The plugin supports both widgetized themes and non-widgetized themes - add the widget manually to your sidebar by placing <?php mow_widget(); ?> in your themes sidebar.php file where you want it to be.

== Installation ==

1. Download the plugin
2. Unzip momentile-on-wordpress.zip
3. Upload the entire momentile-on-wordpress folder to your plugins directory
4. Go to the Plugins page in your WordPress Administration area and click to 'Activate' Momentile on WordPress.

= Then either: =

5. Add your Momentile username on the settings page, and change any other settings as required
6. Go to Appearance->widgets and add the Momentile on WordPress widget to your sidebar

= or: =

5. Open your theme folder
6. Open sidebar.php in a text editor
7. Add <?php mowp_widget('[username]', '[number to show]', '[image width]', '['image shape']); ?> into the code where you want the widget to be (image width and image shape default to '64' and 'square' respectively).

== Frequently Asked Questions ==

= What is my username? =

Your username is the name shown next to your avatar on your profile page.

== Screenshots ==

1. 1x 150px photo
2. 6x 50px square photos with a custom border (set on the options page)

== Changelog ==

= 0.6.2 =
* Fixed to work with Momentile re-release *

= 0.6.1 =
* Check added for fetch_feed error *

= 0.6 =
* Replaced functions deprecated in WP 3.0 - min. requirement now WP 2.8+.

= 0.5.3.2 =
* Fixed messed up changelog

= 0.5.3.1 =
* Fixed title/alt tag mix-up

= 0.5.3 =
* Changed call to mowp_widget in non widgetized themes to allow display of multiple Momentile accounts (apologies is this breaks any themes!)
* Fixed some layout issues
* Tested up to 2.9.1

= 0.5.2.1 =
* Added changelog
* Tested up to WP 2.8.3
* Added plugin directory tags

= 0.5.2 =
* Fixed inconsistent square/thumbail option & option page labels

= 0.5.1 =
* mowp_widget() function call for non-widgetized themes

= 0.5 =
* Initial beta release