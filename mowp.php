<?php
/*
Plugin Name: Momentile On WordPress
Plugin URI: http://mou.me.uk/projects/wordpress/plugins/momentile-on-wordpress/
Description: Displays one or several of your latest <a href="http://momentile.com" target="_blank">Momentile</a> photos on your blog sidebar. Requires WP 2.8+
Tags: Momentile, photos, sidebar, widget, dashboard, rss
Version: 0.6.2
Author: Chris Chrisostomou
Author URI: http://mou.me.uk/
*/

// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

// Changelog:
// 0.6.2 	- Fixed to work with Momentile re-release (3/2/2010)
// 0.6.1 	- Check added for fetch_feed error
// 0.6		- Replaced functions deprecated in WP 3.0 - min. requirement now WP 2.8+.
// 0.5.3.2	- Fixed broken changelog (7/2/10)
// 0.5.3.1	- Fixed title/alt tag mix-up (7/2/10)
// 0.5.3	- Changed call to mowp_widget in non widgetized themes to allow display of multiple Momentile accounts (apologies is this breaks any themes!), few layout fixes, tested up to wp2.9.1 (7/2/10)
// 0.5.2.1	- Added changelog, tested up to wp2.8.3, added plugin directory tags (8/8/09)
// 0.5.2	- Fixed inconsistent square/thumbail option & option page labels  (4/7/09)
// 0.5.1	- mowp_widget() function call for non-widgetized themes  (21/6/09)
// 0.5		- Initial beta release  (18/6/09)



// Add the default options on plugin activation (if they don't already exist)
register_activation_hook(__FILE__, 'mowp_init');
function mowp_init() {
	if ( get_option('mowp_username')		== "" ) { add_option("mowp_username", $value = "chrismou", $deprecated = "", $autoload = "yes"); }
	if ( get_option('mowp_sidebarTitle')	== "" ) { add_option("mowp_sidebarTitle", $value = "Momentile", $deprecated = "", $autoload = "yes"); }
	if ( get_option('mowp_sidebarNum')		== "" ) { add_option("mowp_sidebarNum", $value = "1", $deprecated = "", $autoload = "yes"); }
	if ( get_option('mowp_sidebarMaxWidth')	== "" ) { add_option("mowp_sidebarMaxWidth", $value = "64", $deprecated = "", $autoload = "yes"); }
	if ( get_option('mowp_imageShape')		== "" ) { add_option("mowp_imageShape", $value = "square", $deprecated = "", $autoload = "yes"); }
	if ( get_option('mowp_linkBackText')	== "" ) { add_option("mowp_linkBackText", $value = "More", $deprecated = "", $autoload = "yes"); }
}


function mowp_widget($username, $num, $maxWidth=64, $imageShape='square', $linkBackText='More') {
	$rss = @ fetch_feed('http://momentile.com/'.$username.'/rss');
	
	echo '<div class="mowp_widget sidebarwidget">';
	
	if (!is_wp_error( $rss )) {
		$items = $rss->get_items( 0, $rss->get_item_quantity($num) );
		echo '<p class="mowp_tiles">';
			foreach ( $items as $item ) {
				preg_match('/<img src="(.*)" alt/', $item->get_description(), $photo);
				preg_match('/&rsquo;s (.*) &lsquo;Tile/', $item->get_title(), $date);
				$title = "Posted ".date('F jS, Y', strtotime($date[1]));
				$finalPhoto = mowp_get_image_size($photo[1], $imageShape, $maxWidth);
				
				echo '<a href="' . $item->get_permalink() . '" title="'.$title.'"';
				if (get_option('mowp_linkInlineStyle')) {
					echo ' style="' . get_option('mowp_linkInlineStyle') . '"';
				}
				if (get_option('mowp_linkClass')) {
					echo ' class="' . get_option('mowp_linkClass') . '"';
				}
				echo '>';
					echo '<img src="'.$finalPhoto.'" style="width:'.$maxWidth.'px;"';
					if (get_option('mowp_inlineStyle')) {
						echo ' style="' . get_option('mowp_inlineStyle') . '"';
					}
					if (get_option('mowp_class')) {
						echo ' class="' . get_option('mowp_class') . '"';
					}
					echo '  alt="'.$item->get_title().'" />';
				echo '</a>';
			}
			echo '<span class="clear"></span>';
		echo '</p>';
		if ($linkBackText != "") {
			echo '<p class="mowp_linkback_text"><a href="' . $rss->get_link() . '" target="_blank" class="more"><span>' . $linkBackText . '</span></a></p>';
		}
	} else if (is_wp_error( $rss )) {
		echo '<p>No items to display</p>';
	} else {
		echo  '<p>Set your momentile username on the momentile-on-wordpress settings page.</p>';
	}
	echo '</div>';
}


function mowp_get_image_size($photo, $imageShape, $maxWidth) {
	if ($imageShape == 'square' ) {
		if ((int)$maxWidth <= 32 ) {
			return str_replace('/128', '/32', $photo);
		} else if ((int)$maxWidth <= 64 ) {
			return str_replace('/128', '/64', $photo);
		} else if ((int)$maxWidth <= 128 ) {
			return $photo;
		} else {
			return str_replace('/128', '/lrg', $photo);
		}
	} else {
		return str_replace('/128', '/lrg', $photo);
	}
}

// Sidebar widget controls
function mowp_sidebar() {
	if ( !function_exists('wp_register_sidebar_widget') )
			return;
	
	function mowp_updates_sidebar_widget($args) {
		extract($args);
		echo $before_widget;
		echo $before_title;
		echo get_option('mowp_sidebarTitle');
		echo $after_title;
		mowp_widget(get_option('mowp_username'), get_option('mowp_sidebarNum'), get_option('mowp_sidebarMaxWidth'), get_option('mowp_imageShape'), get_option('mowp_linkBackText'));
		echo $after_widget;
	}
	wp_register_sidebar_widget('mowp_updates_sidebar_widget', 'Momentile on WordPress', 'mowp_updates_sidebar_widget');
	
	
	
	function mowp_updates_widget_control() {
		if (isset($_POST['mowp_widget_updatesoptions_action']) && $_POST['mowp_widget_updatesoptions_action'] == 'mowp_widget_updatesoptions_update_widget_options') {
			update_option('mowp_sidebarTitle', $_POST['mowp_widget_updatesoptions_widget_title']);
		}
		print('
			<p><label for="mowp_widget_updatesoptions_widget_title">Widget title<br /><input id="mowp_widget_updatesoptions_widget_title" name="mowp_widget_updatesoptions_widget_title" type="text" value="' . get_option('mowp_sidebarTitle') . '" /></label></p>
			<input type="hidden" id="mowp_widget_updatesoptions_action" name="mowp_widget_updatesoptions_action" value="mowp_widget_updatesoptions_update_widget_options" />
		');
	}
	wp_register_widget_control('Momentile on WordPress', 'mowp_updates_widget_control', 200, 100);
	
}

add_action('widgets_init', 'mowp_sidebar');

// Options Page
function mowp_options() {
	#$wpVersion = (float) $GLOBALS['wp_version'];
	?>
    <div class="wrap">
		<form method="post" action="options.php" style="margin-bottom: 60px;">
			<h2><?php echo __('Momentile on WordPress Options', 'mowp') ?></h2>
			<?php wp_nonce_field('update-options') ?>
			<h2>General</h2>
			<table class="form-table" style="margin: 5px 0 20px;">
				<tr>
					<th><label for="mowp_username">Username</label></th>
					<td><input type="text" name="mowp_username" value="<?php echo get_option('mowp_username'); ?>" /></td>
				</tr>
				<tr>
					<th><label for="mowp_sidebarNum">Number of images to display on your sidebar:</label></th>
					<td>
						<select name="mowp_sidebarNum">
							<?php for ($a=1;$a<=7;$a++) { ?>
								<option value="<?php echo $a; ?>"<?php if ($a==get_option('mowp_sidebarNum')) echo ' selected="selected"'; ?>><?php echo $a; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="mowp_sidebarMaxWidth">Maximum image width:</label></th>
					<td>
						<input type="text" name="mowp_sidebarMaxWidth" value="<?php echo get_option('mowp_sidebarMaxWidth'); ?>" />px
						<br />
						<em>Square images are only available up to 128px.  Anything over that will be the original shape</em>
					</td>
				</tr>
				<tr>
					<th><label for="mowp_imageShape">Image shape:</label></th>
					<td>
						<select <?php if ((int)get_option('mowp_sidebarMaxWidth') > 128) echo ' disabled="disabled" name="mowp_imageShape_dummy"'; else echo ' name="mowp_imageShape"'; ?>>
							<option value="square"<?php if (get_option('mowp_imageShape')=='square') echo ' selected="selected"'; ?>>Square</option>
							<option value="original"<?php if (get_option('mowp_imageShape')=='original') echo ' selected="selected"'; ?>>Original</option>
						</select>
						<?php 
						if ((int)get_option('mowp_sidebarMaxWidth') > 128) {
							echo '<input type="hidden" name="mowp_imageShape" value="' . get_option('mowp_imageShape') . '" />';
						}
						?>
					</td>
				</tr>
				<tr>
					<th>Text to use for the link back to your momentile page:</th>
					<td>
						<input type="text" name="mowp_linkBackText" value="<?php echo get_option('mowp_linkBackText'); ?>" />
						<br />
						<em>Leave this blank if you don't want to display a link back to momentile</em>
					</td>
				</tr>
			</table>
			<h2>Advanced</h2>
			<table class="form-table" style="margin: 5px 0 20px;">
				<tr>
					<th><label for="mowp_linkInlineStyle">Inline Style <em>(optional)</em>:</label></th>
					<td>
						<input type="text" name="mowp_linkInlineStyle" id="mowp_linkInlineStyle" value="<?php echo get_option('mowp_linkInlineStyle'); ?>" />
						<br/>
						<em>If you know CSS you can add an inline style in this box that will be applied to the &lt;a&gt; tag</em>
					</td>
				</tr>
				<tr>
					<th><label for="mowp_class">CSS Class <em>(optional)</em>:</label></th>
					<td>
						<input type="text" name="mowp_linkClass" id="mowp_linkClass" value="<?php echo get_option('mowp_linkClass'); ?>" />
						<br />
						<em>If you would like to add a CSS class to the link (&lt;a&gt; tag), enter the class name here</em>
					</td>
				</tr>
				<tr>
					<th><label for="mowp_inlineStyle">Inline Style <em>(optional)</em>:</label></th>
					<td>
						<input type="text" name="mowp_inlineStyle" id="mowp_inlineStyle" value="<?php echo get_option('mowp_inlineStyle'); ?>" />
						<br/>
						<em>If you know CSS you can add an inline style in this box that will be applied to the &lt;img&gt; tag</em>
					</td>
				</tr>
				<tr>
					<th><label for="mowp_class">CSS Class <em>(optional)</em>:</label></th>
					<td>
						<input type="text" name="mowp_class" id="mowp_class" value="<?php echo get_option('mowp_class'); ?>" />
						<br />
						<em>If you would like to add a CSS class to the image (&lt;img&gt; tag), enter the class name here</em>
					</td>
				</tr>
			</table>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="mowp_username, mowp_sidebarNum, mowp_sidebarMaxWidth, mowp_inlineStyle, mowp_class, mow_linkInlineStyle, mowp_linkClass, mowp_imageShape, mowp_linkBackText" />
            <p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options »') ?>" /></p>
	    </form>
	    <h2>How to use</h2>
	    <h4>Widgetized sidebar</h4>
	    <ol>
	    	<li>Update the options above</li>
	    	<li>Navigate to Appearance &gt; Widgets</li>
	    	<li>Add it to your sidebar (you can also change the widget title here)</li>
	    </ol>
	    <h4>Non-widgetized sidebar</h4>
	    <ol>
	    	<li>Go to sidebar.php (or whatever your the file that builds the sidebar in your theme is called)</li>
	    	<li>Add &lt;?php mowp_widget('[username]', '[number to show]', '[image width]', '['image shape']); ?&gt into the code where you want the widget to be (replacing the [] bits with the required values. Image width and image shape are optional and default to '64' and 'square' respectively).</li>
	    </ol>
	    <p>&nbsp;</p>
	</div>
    <?php
}

function mowp_menus() {
	#if (current_user_can('manage_options')) {
		if (function_exists('add_options_page')) {
			#add_options_page(__('Momentile on WordPress', 'mowp'), __('Momentile on WordPress', 'mowp'), 8, __FILE__, 'mowp_options');
			add_submenu_page('options-general.php', __('Momentile on WordPress', 'mowp'), __('Momentile on WordPress', 'mowp'), 'install_plugins', __FILE__, 'mowp_options');
		}
	#}
}

add_action('admin_menu', 'mowp_menus');

?>