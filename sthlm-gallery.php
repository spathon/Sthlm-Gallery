<?php 
/*
Plugin Name: Sthlm Gallery
Plugin URI: http://spathon.com
Description: Create galleries with the images uploaded to the media library through an easy drag and drop interface. "Media->Gallery"
Version: 0.3
Author: Patrik Spathon, Jonatan Fried, Linda Eriksson
Author URI: http://spathon.com
*/

/**
 * @todo
 *
 * Edit img alt/title and so on in colorbox (dbclick? for edit or smal edit button on hover?)
 * Split css and load right on right page
 * add categories/tags to images
 * settings page
 * pagination
 * better insert shortcode
 * better structure on code
 * and much more
 *
 * clean the code
 *
 *
 *
 * META _thumbnail_id
 *
 */


define('STHLM_GALLERY_PLUGIN_URL', WP_PLUGIN_URL.'/'. dirname( plugin_basename(__FILE__) ) );
load_plugin_textdomain('sthlm_gallery', dirname(plugin_basename( __FILE__ )) ."/lang" );


// check if the version of WP is 3.0 or greater
if ( version_compare( $wp_version, '3.0', '>=' ) ):

	
	// templates for common html
	include('templates.php');
	// the gallery class
	include('gallery.class.php');








else:
	// if user has to old version of wp print a message
	add_action('admin_notices', "sthlm_gallery_need_wp3");
	function sthlm_gallery_need_wp3(){
		echo '<div class="error fade"><p>'. __('The WordPress version you are using is to old, please update for your on safety', 'sthlm_gallery') .'</p></div>';
	}
endif; // wp 3.0 or not





//Create new instance of class
if (class_exists("sthlm_gallery")) {
	$sthlm_gallery = new sthlm_gallery();
}
//Actions and Filters
if(isset($sthlm_gallery)){

	// Administration Actions
	// add menu page
	add_action('admin_menu', array($sthlm_gallery,'init'), 1);
	// create post type
	add_action('init', array($sthlm_gallery, 'sthlm_add_gallery_post_type'));


	// AJAX
	add_action('wp_ajax_sthlm_load_lightbox_form_ajax', array($sthlm_gallery, 'sthlm_load_lightbox_form_ajax')); // add new gallery

	add_action('wp_ajax_sthlm_gallery_ajax_add', array($sthlm_gallery, 'sthlm_gallery_ajax_add')); // add new gallery
	add_action('wp_ajax_sthlm_gallery_ajax_delete', array($sthlm_gallery, 'sthlm_gallery_ajax_delete')); // delete a gallery
	add_action('wp_ajax_sthlm_gallery_ajax_save_order', array($sthlm_gallery, 'sthlm_gallery_ajax_save_order')); // save the order in the gallery
	// insert the correct images when folder is chosen
	add_action('wp_ajax_sthlm_get_current_gallery_images_ajax', array($sthlm_gallery, 'sthlm_get_current_gallery_images_ajax'));
	
	add_action('wp_ajax_sthlm_gallery_edit_image_data', array($sthlm_gallery, 'sthlm_gallery_edit_image_data'));	

	/*  FILTERS  */
	add_action('wp_ajax_sthlm_gallery_query_attachments', array($sthlm_gallery, 'sthlm_gallery_query_attachments'));
	// filter by tag
	add_action('wp_ajax_sthlm_filter_by_tags_ajax', array($sthlm_gallery, 'sthlm_filter_by_tags_ajax'));
}









/**
 *
 *   DISPLAY
 *
 * "front end"
 *
 */








/**
 * Add/remove javascript
 */
add_action('wp_print_scripts', 'sthlm_gallery_include_script');
function sthlm_gallery_include_script(){
	if (!is_admin()){
		// the scripts
		$js_folder = STHLM_GALLERY_PLUGIN_URL . "/js/";
		wp_enqueue_script("colorbox", $js_folder ."jquery.colorbox-min.js", array('jquery'), "", true);
		wp_enqueue_script("sthlm_gallery", $js_folder ."sthlm-gallery.js", array('jquery', 'colorbox'), "", true);
	}
}


/**
 * Deregister CSS
 *
 * wp-pagenavi
 */
add_action( 'wp_print_styles', 'sthlm_gallery_include_styles' );
function sthlm_gallery_include_styles() {
	wp_enqueue_style('colorbox', STHLM_GALLERY_PLUGIN_URL.'/css/colorbox.css' );
	wp_enqueue_style('sthlm_gallery', STHLM_GALLERY_PLUGIN_URL.'/css/sthlm-gallery.css' );
}






/**
 *
 *   Add a button to the editor
 *
 */

add_action('media_buttons_context', 'add_sthlm_gallery_button');
function add_sthlm_gallery_button($context){
	$img = STHLM_GALLERY_PLUGIN_URL .'/images/img.png';
	$out = '<a href="#TB_inline?width=640&inlineId=add_sthlm_gallery" class="thickbox" title="' . __("L&auml;gg till galleri", 'sthlm_gallery') . '"><img src="'.$img.'" alt="' . __("L&auml;gg till galleri", 'sthlm_gallery') . '" /></a>';
	return $context . $out;
}
add_action('admin_footer',  'sthlm_gallery_add_mce_popup');
function sthlm_gallery_add_mce_popup(){
	?>
	<script>
		
		
		jQuery(document).ready(function($){


			var $selectGallery = $('#sthlm_select_gallery');

			// Show thumb on select gallery
			$selectGallery.live('change', function(){
				var $option = $selectGallery.find('option:selected'),
					numImg = $option.attr('data-gallery-count'),
					src = $option.attr('data-gallery-thumb');


				// Show the number of images
				$('#sthlm_gallery_num_img').text(numImg);

				// set the thumb src
				$('#sthlm_preview_thumb').attr('src', src);
			});





			$('#sthlm_display_style').find('img').live('click', function(){
				var style = $(this).attr('data-style');
				$('#sthlm_display_style_input').attr('value', style);

				$('#sthlm_display_style').find('img').removeClass('sthlm-selected-style');
				$(this).addClass('sthlm-selected-style');
			});


			var win = window.dialogArguments || opener || parent || top;
			$('#add_sthlm_gallery_shortcode').click(function(){
				var galleryID = $('#sthlm_select_gallery').val(),
					rows = $('#sthlm_rows').val(),
					rowsOut = '',
					style;

				// number of img per row
				if(rows > 0){
					rowsOut = ' rows='+ rows;
				}

				// display style
				style = ' style="'+ $('#sthlm_display_style_input').attr('value') +'"';


			
				win.send_to_editor('[sthlm_gallery id='+ galleryID + rowsOut + style +' ]');

				// close thickbox
				tb_remove();
				return false;
			});

		});

	</script>

	<div id="add_sthlm_gallery" style="display:none;">
		<div class="wrap" id="sthlm_add_shortcode">



			<div class="sthlm-dropdown-gallery">
			<?php
			/**
			 *   List all galleries
			 */
			$args = array(
				'post_type' => 'sthlm_gallery',
				'numberposts' => -1
			);
			$gallery = get_posts($args);
			if(!empty($gallery)): ?>
				
				<div class="sthlm-dropdown-box">

					<h2 class="sthlm-dropdown-title"><?php _e('Select gallery', 'sthlm_gallery') ?></h2>
					<select id="sthlm_select_gallery">
						<?php 
						// Echo the galleries in option
						$i = 0;
						foreach ( (array) $gallery as $g ):

							// get the thumbnail
							$image_id = get_post_thumbnail_id($g->ID);
							$image_url = wp_get_attachment_image_src($image_id,'thumbnail');
							$image_url = $image_url[0];

							// if the gallery don't have any images
							if(empty($image_url)) continue;

							$image_ids = get_post_meta($g->ID, '_img_order', true);
							$number_images = count($image_ids);

							// save the first img src
							if($i == 0){ 
								$first_thumb = $image_url;
								$first_number = $number_images;
								$i = 1;
							}

							// the option
							echo '<option value="'.$g->ID.'" data-gallery-count="'.$number_images.'" data-gallery-thumb="'.$image_url.'">'.
									$g->post_title.'</option>';

						endforeach;?>

					</select>

					<div class="sthlm-gallery-img-count">
						<?php _e('Number of images:', 'sthlm_gallery'); ?>
						<span id="sthlm_gallery_num_img"><?php echo $first_number; ?></span>
					</div>

				</div>

			<?php endif; ?>

				<img src="<?php echo $first_thumb; ?>" id="sthlm_preview_thumb" alt="Thumb" />
			</div>





			<label><?php _e('Rader', 'sthlm_gallery'); ?>
				<input type="text" id="sthlm_rows" />
			</label>
			<div id="sthlm_display_style">
				<img class="sthlm-style-thumbs sthlm-selected-style" src="<?php echo STHLM_GALLERY_PLUGIN_URL; ?>/images/style-mini-thumbs.gif" data-style="thumbs" />
				<img class="sthlm-style-big-lightbox" src="<?php echo STHLM_GALLERY_PLUGIN_URL; ?>/images/style-big-lightbox.gif" data-style="big_lightbox" />
				<img class="sthlm-style-big-with-thumbs" src="<?php echo STHLM_GALLERY_PLUGIN_URL; ?>/images/style-one-big.gif" data-style="big_with_thumbs" />
				<input type="hidden" name="sthlm_display_style_input" id="sthlm_display_style_input" value="">
			</div>
			
			
			
			<a class="button-secondary" href="#" id="add_sthlm_gallery_shortcode"><?php _e('Infoga', 'sthlm_gallery'); ?></a>
		</div>
	</div>

	<?php
}







/**
 *
 *   SHORTCODE
 *
 */
function sthlm_gallery_shortcode($atts, $content = null){
	extract(shortcode_atts(array(
		"id" => false,
		"rows" => 4,
		"style" => 'thumbs'
	), $atts));

	$out = "";

	if($id){
		$meta = get_post_meta($id, '_img_order', true);
		$i = 0;
		$row_i = 0;
		if(!empty($meta)){
			
			switch ($style){
				case 'big_lightbox':
					include('display/big_lightbox.php');
					break;

				case 'big_with_thumbs':
					include('display/big_with_thumbs.php');
					break;

				case 'thumbs':
				default:
					include('display/thumbs.php');
					break;
			}


		}// end if
	}
	return $out;

}
add_shortcode('sthlm_gallery', 'sthlm_gallery_shortcode');

































/* button on edit page
add_action( 'media_buttons' , 'sthlm_gallery_media_button', 100);

function sthlm_gallery_media_button($content){
	echo '<a  class="thickbox" id="add_sthlm_gallery" href="#">
		<img alt="Add Sthlm Gallery" src="images/media-button-image.gif?ver=20100531"></a>';
}

*/


