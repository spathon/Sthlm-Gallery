<?php

$out .= '<div class="sthlm-gallery" id="sthlm_gallery_'.$id.'">';
	foreach (  $meta as $m){

		$row_i++;
		$img = get_post($m);
		$class = 'sthlm-thumb sthlm-thumb-'. $img->ID .' sthlm-thumb-col-'.$row_i;

		if($row_i == 1){
			$class .= ' sthlm-thumb-first';
		}elseif($row_i >= $rows){
			$class .= ' sthlm-thumb-last';
		}

		$src = wp_get_attachment_image_src($img->ID, 'thumbnail'); // The thumb
		$href = wp_get_attachment_image_src($img->ID, 'full'); // The img
		$out .= '<div class="'.$class.'" id="sthlm_thumb_'.$i.'">';
			$out .= '<a class="colorbox" href="'. $href[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" rel="sthlm_gallery['.$id.']" />';
				$out .= '<img src="'. $src[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" />';
			$out .= '</a>';
		$out .= '</div>';

		if($row_i >= $rows){
			$row_i = 0;
			$out .= '<div class="clear"></div>';
		}

		$i++;

	} // end foreach
echo '</div>'; // end .sthlm-gallery