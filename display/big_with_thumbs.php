<?php

$out .= '<div class="sthlm-gallery" id="sthlm_gallery_'.$id.'">';
	$first = true;
	foreach (  $meta as $m){

		$img = get_post($m);
		$href = wp_get_attachment_image_src($img->ID, 'large'); // The img
		$thumb = wp_get_attachment_image_src($img->ID, 'thumbnail'); // The img

		if($first){
			$src = wp_get_attachment_image_src($img->ID, 'large'); // The thumb
			$out .= '<div class="big-with-thumb-main">';
				$out .= '<img src="'. $src[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" />';
			$out .= '</div>';
			$first = false;
		}else{
			$out .= '<a class="big-with-thumb-a" href="'. $href[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" rel="sthlm_gallery['.$id.']" />';
				$out .= '<img src="'. $thumb[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" />';
			$out .= '</a>';
		}
	} // end foreach
$out .= '</div>'; // end .sthlm-gallery