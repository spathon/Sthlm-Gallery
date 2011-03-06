
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="wrap">
	<div class="icon32" id="icon-index"><br></div>
	<h2><?php _e('Sthlm Gallery', 'sthlm_gallery'); ?></h2>
	


	<!-- List all galleries -->
	<div id="sthlm_galleries">

		<?php
		/**
		 *   List all galleries
		 */
		$args = array(
			'post_type' => 'sthlm_gallery',
			'numberposts' => -1
		);
		$gallery = get_posts($args);
		if(!empty($gallery)): foreach ( (array) $gallery as $g ):
			sthlm_gallery_template($g->ID, $g->post_title);
		endforeach; endif; ?>

		<!--   Add gallery   -->
		<a id="sthlm_add_gallery_button" class="sthlm-gallery-add" href="#add_sthlm_gallery_form"><?php _e('Lägg till galleri', 'sthlm_gallery'); ?></a>

		<div class="clear"></div>
	</div>


	

	
	
	
	
	
	
	
	<div id="sthlm_img_wrapper">

		<!--   Save images   -->
		<div id="sthlm_current_gallery_images_settings">
			<span id="sthlm_uppdate_gallery_images">
				<a href="#" class="button-secondary">Spara </a><span class="sthlm-gallery-loader hidden">&nbsp;</span>
			</span>
		</div>


		<!--   FILTERS   -->
		<div id="sthlm_img_filters">

			<!--   Select by date   -->
			<div id="sthlm-gallery-datepicker"><?php _e('Visa bilder från', 'sthlm_gallery');?>
			<?php $attachments = get_posts('post_type=attachment&numberposts=-1');
			foreach($attachments as $attachment){
				if(strstr($attachment->post_mime_type, 'image')){
					$year = substr($attachment->post_date, 0, 4);
					$dates[$year][] = mktime(0, 0, 0, substr($attachment->post_date, 5, 2), 1, $year);
				}
			}
			echo '<select id="wpsthlm-date"><option value="">'.__('Alla datum', 'sthlm_gallery').'</option>';
			foreach($dates as $year => $months){
				echo '<option value="'.$year.'">--'.$year.'--</option>';
				foreach(array_unique($months) as $month){
					echo '<option value="'.date('Y/m', $month).'">'.date('M', $month).'</option>';
				}
			}
			echo '</select>'; ?>
			</div>

			<!--   Search for img   -->
			<div id="sthlm-gallery-filter">
				<input id="wpsthlm-filter" type="text" value="<?php _e('Fritextsök', 'sthlm_gallery'); ?>" size="10" />
				<input class="button-secondary" type="submit" value="Sök" />
			</div>

			<!--   Filter by tags   -->
			<div id="sthlm_filter_by_tags">
				<label for="sthlm_select_tag"><?php _e('Taggar', 'sthlm_gallery'); ?></label>
				<?php
				$args = array(
					'show_option_all' => 'V&auml;lj tag',
					'taxonomy' => 'img_cat',
					'name' => 'sthlm_select_tag',
					'id' => 'sthlm_select_tag',
					'orderby' => 'name'
				);
				wp_dropdown_categories($args);
				?>
			</div>

			<!--   Add media   -->
			<div class="add-media">
				<a title="Add an Image" class="thickbox" id="add_image" href="media-upload.php?type=image&amp;TB_iframe=1&amp;width=640&amp;height=400">
					<img alt="Add an Image" src="images/media-button-image.gif?ver=20100531" /><?php _e('Lägg till bild', 'sthlm_gallery'); ?>
				</a>
			</div>

		</div>





		<!-- Table for equal height always -->
		<table id="sthlm_img_table">
			<tr>
				<!--   Gallery images   -->
				<td class="sthlm-images-container" id="sthlm_current_gallery_images">
					<ol id="sthlm_init_message_list">
						<li><?php _e('Select a gallery above or create a new', 'sthlm_gallery'); ?></li>
						<li><?php _e('Drag the images here', 'sthlm_gallery'); ?></li>
					</ol>
					
				</td>
				<!--   All images   -->
				<td class="sthlm-images-container" id="sthlm_all_images">
					<?php
					$args = array(
						'post_type' => 'attachment',
						'post_mime_type' => 'image',
						'numberposts' => 32,
						'orderby' => 'menu_order',
						'order' => 'ASC'
					);
					$images = get_posts($args);

					// print most images
					if(!empty($images)): foreach ( (array) $images as $img ):
						sthlm_img_thumb_template($img);
					endforeach; else:  
						_e('Inga bilder existerar.', 'sthlm_gallery');
					endif; ?>
				</td>
			</tr>
		</table>
	</div>
	
	
	
	
	


<?php /*
	<div id="wrapp_all_sthlm_images">

		<!-- Gallery images -->
		<div id="images_in_sthlm_gallery_wrapper">
			<div id="current_sthlm_gallery_settings">
				<span id="uppdate_sthlm_gallery_images">
					<a href="#" class="button-secondary">Spara </a><span class="sthlm-gallery-loader hidden">&nbsp;</span>
				</span>
			</div>
			<div class="sthlm-gallery-inside clearfix sthlm-gallery-inside-current">	</div>
		</div>


		<!-- All images -->
		<div id="gallery_sthlm_thumbs_wrapper">
			<div id="all_img_sthlm_gallery_filter">

				<div id="sthlm-gallery-datepicker"><?php _e('Visa bilder från', 'sthlm_gallery');?>
				<?php $attachments = get_posts('post_type=attachment&numberposts=-1');
				foreach($attachments as $attachment){
					if(strstr($attachment->post_mime_type, 'image')){
						$year = substr($attachment->post_date, 0, 4);
						$dates[$year][] = mktime(0, 0, 0, substr($attachment->post_date, 5, 2), 1, $year);
					}
				}
				echo '<select id="wpsthlm-date"><option value="">'.__('Alla datum', 'sthlm_gallery').'</option>';
				foreach($dates as $year => $months){
					echo '<option value="'.$year.'">--'.$year.'--</option>';
					foreach(array_unique($months) as $month){
						echo '<option value="'.date('Y/m', $month).'">'.date('M', $month).'</option>';
					}
				}
				echo '</select>'; ?>
				</div>
				
				<div id="sthlm-gallery-filter">
					<input id="wpsthlm-filter" type="text" value="<?php _e('Fritextsök', 'sthlm_gallery'); ?>" size="10" />
					<input class="button-secondary" type="submit" value="Sök" />
				</div>
										
				<div class="add-media">
					<a title="Add an Image" class="thickbox" id="add_image" href="media-upload.php?type=image&amp;TB_iframe=1&amp;width=640&amp;height=400">
						<img alt="Add an Image" src="images/media-button-image.gif?ver=20100531" /><?php _e('Lägg till bild', 'sthlm_gallery'); ?>
					</a>
				</div>

			</div>
			<div class="sthlm-gallery-inside sthlm-gallery-inside-all clearfix">
			<?php
			$args = array(
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => 32,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$images = get_posts($args);

			$i = 0;
			// loop all images
			if(!empty($images)): foreach ( (array) $images as $img ):

				sthlm_gallery_thumb_template($img);

			endforeach; else:  ?>

				Inga bilder existerar.

			<?php endif; ?>
				<div class="clear"></div>
		</div></div>
	</div> */ ?>
</div>





<!--   Put error & success messages in here   -->
<div id="sthlm_success_error_messages" class="hidden radius-5"></div>



<?php
/**
 *   COLORBOX
 *
 * - Add gallery
 * - Edit img
 */
?>
<div class="hidden">
	<div id="sthlm_lightbox"></div>
</div>




