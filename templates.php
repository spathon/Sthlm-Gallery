<?php


/*
 *
 *    Template for the thumbnails
 *
 */
function sthlm_img_thumb_template($img){

	// get the thumbnail
	$src = wp_get_attachment_image_src($img->ID, 'thumbnail'); // The img
	?>
	<div class="sthlm-thumb sthlm-thumb-<?php echo $img->ID; ?>" data-thumb-id="<?php echo $img->ID; ?>">
		<div class="remove-sthlm-thumb"></div>
		<img src="<?php echo $src[0]; ?>" alt="<?php echo esc_attr($img->post_title); ?>" />

		<?php sthlm_img_thumb_data_template($img); ?>
		
	</div>
<?php
}
// template for the img info
function sthlm_img_thumb_data_template($img){ ?>

	<div class="sthlm-thumb-data">
		<div class="sthlm-thumb-id hidden"><?php echo $img->ID; ?></div>
		<div class="sthlm-thumb-title"><?php echo $img->post_title; ?></div>
		<div class="sthlm-thumb-excerpt hidden"><?php echo $img->post_excerpt; ?></div>
		<div class="sthlm-thumb-content hidden"><?php echo $img->post_content; ?></div>
		<div class="sthlm-thumb-guid hidden"><?php echo $img->guid; ?></div>
		<a class="sthlm-gallery-edit-image-data" href="#sthlm-gallery-image-data">
			<?php _e('Redigera', 'sthlm_gallery'); ?>
		</a>
	</div>
<?php
}







/*
 *
 *    Template for the gallery
 *
 */
function sthlm_gallery_template($id, $title){ ?>
	<div class="sthlm-gallery" data-gallery-id="<?php echo $id; ?>" >
		<div class="remove-sthlm-gallery"></div>
		<div class="sthlm-gallery-title"><?php echo $title; ?></div>
		<?php echo get_the_post_thumbnail($id, 'thumbnail'); ?>
	</div>
<?php
}