<?php

$out .= '<div class="sthlm-gallery" id="sthlm_gallery_'.$id.'">';
	$first = true;
	foreach (  $meta as $m){

		$img = get_post($m);
		$href = wp_get_attachment_image_src($img->ID, 'full'); // The img

		if($first){
			$src = wp_get_attachment_image_src($img->ID, 'medium'); // The thumb
			$out .= '<a class="colorbox" href="'. $href[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" rel="sthlm_gallery['.$id.']">';
				$out .= '<img src="'. $src[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" />';
			$out .= '</a>';
			$first = false;
		}else{
			$out .= '<a class="colorbox hidden" href="'. $href[0].'" alt="'. $img->post_title .'" title="'.$img->post_content.'" rel="sthlm_gallery['.$id.']" />';
				$out .= $img->post_content;
			$out .= '</a>';
		}
	} // end foreach
$out .= '</div>'; // end .sthlm-gallery