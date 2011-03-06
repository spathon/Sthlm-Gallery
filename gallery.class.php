<?php
class sthlm_gallery {

	public $prefix = "sthlm_gallery_",
			$menu_title, // set in init for translation
			$menu_slug = 'sthlm_gallery',
			$capability = 4,
			$title = 'Sthlm Gallery',
			$meta_order = '_img_order', // don't cange used else were
			$url = STHLM_GALLERY_PLUGIN_URL; // name for meta data




	function init() {

		// set the title for
		$this->menu_title = __('Galleri', 'sthlm_gallery');

		// check if theme has thumbnail support else activate for sthlm_gallery post_type
		if(!current_theme_supports( 'post-thumbnails' ))
			add_theme_support('post-thumbnails', array('sthlm_gallery'));
		
		$admin_page = add_media_page($this->title, $this->menu_title, $this->capability, $this->menu_slug, array(&$this, 'gallery_page'));
		// javascript på andmin sidan och med variablen ovan använder scripet bara på den sidan
		// new javascript on edit or the same?
		add_action('admin_print_scripts-'. $admin_page, array(&$this,'js_admin'));
		// admin css
		//add_action('admin_print_styles-'. $admin_page, array(&$this,'css_admin'));
		add_action('admin_print_styles', array(&$this,'css_admin'));

		// js on all admin pages
		//add_action('admin_print_scripts', array(&$this,'js_admin_edit'));
		
		
	}

	/*
	 *   The gallery page
	 */
	function gallery_page(){
		include('admin-page.php');
	}




	/**
	 *    ADD ADMIN JAVASCRIPT
	 *
	 *
	 */
	function js_admin(){
		wp_enqueue_script('thickbox');
		wp_enqueue_script('colorbox', $this->url . '/js/jquery.colorbox-min.js', array('jquery'));
		wp_enqueue_script('sthlm_gallery_admin_js', $this->url . '/js/sthlm-admin-page.js', array('jquery', 'colorbox','jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'));
	}
	/**
	 *    ADD ADMIN CSS
	 *
	 *
	 */
	function css_admin(){
		wp_enqueue_style('thickbox');
		wp_enqueue_style('colorbox', $this->url . '/css/colorbox.css');
		wp_enqueue_style('sthlm_gallery_admin_css', $this->url . '/css/sthlm-admin.css');
	}








	/**
	 * CREATE THE POST TYPE PUFFAR
	 *
	 *
	 *
	 */
	function sthlm_add_gallery_post_type() {
		register_post_type(
			'sthlm_gallerya',
			array(
				'labels' => array(
					'name' => __('Gallerier', 'sthlm_gallery'), //general name for the post type, usually plural. The same as, and overridden by $post_type_object->label
					'singular_name' => __('Galleri', 'sthlm_gallery'), // - name for one object of this post type. Defaults to value of name
				), // (string) (optional) A plural descriptive name for the post type marked for translation.
				'description' => __('Gallerier', 'sthlm_gallery'), //(string) (optional) A short descriptive summary of what the post type is. Defaults to blank.
				'public' => false, // (boolean) (optional) Whether posts of this type should be shown in the admin UI.
				'exclude_from_search' => true, //(boolean) (importance) Whether to exclude posts with this post type from search results.
				//'publicly_queryable' => true,//(boolean) (optional) Whether post_type queries can be performed from the front page.
				'show_ui' => false, // (boolean) (optional) Whether to generate a default UI for managing this post type.
				//'inherit_type' => '', //(string) (optional) The post type from which to inherit the edit link and capability type.
				'capability_type' => 'post',//(string) (optional) The post type to use for checking read, edit, and delete capabilities.
				'hierarchical' => false, // (boolean) (optional) Whether the post type is hierarchical.
				'show_in_nav_menus' => false,
				'supports' => array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'excerpt',
					'custom-fields',
					'trackbacks',
					'comments',
					'revisions',
					'page-attributes' //(parent, template, and menu order)
				)#,
				#'register_meta_box_cb' => array(&$this, 'ps_init_puff_meta_boxes') // (string) (optional) Provide a callback function that will be called when setting up the meta boxes for the edit form. Do remove_meta_box() and add_meta_box() calls in the callback.
			)
		);

		// add category to images // TAGS or CATEGORIES?
		register_taxonomy('img_cat', 'attachment', array( 'hierarchical' => false, 'show_ui' => true	) );
		register_taxonomy_for_object_type('img_cat', 'attachment');
		add_post_type_support('attachment', 'img_cat');

	}










	/**
	 *
	 *    AJAX
	 *
	 */

	// load admin form
	function sthlm_load_lightbox_form_ajax(){
		include('forms.php');
		die();
	}



	
	// add a gallery
	function sthlm_gallery_ajax_add(){

		$title = $_POST['title'];
		$desc = $_POST['desc'];

		// check if title is set
		if(!empty($title)){
			// Create post object
			$my_post = array(
				'post_title' => $title,
				'post_content' => $desc,
				'post_status' => 'publish',
				'post_type' => 'sthlm_gallery'
			);

			// Insert the post into the database
			$id = wp_insert_post( $my_post );

			sthlm_gallery_template($id, $title);
			//echo  '<div class="sthlm-gallery" data-id="'. $id .'" >'.$title.'</div>';

			die();
		}
	}

	// delete gallery
	function sthlm_delete_gallery_ajax(){

		$id = (int) $_POST['id'];

		// check if the id is of the gallery post typ
		if($this->is_gallery($id)){
			wp_delete_post( $id, $force_delete = true );
		}else{
			echo 'error';
		}
		die();
	}


	// img save order
	function sthlm_gallery_ajax_save_order(){

		$order = $_POST['order'];
		$id = (int) $_POST['id']; // gallery id

		if($this->is_gallery($id)){
			// update the gallery thumbnail
			update_post_meta($id, '_thumbnail_id', $order[0]);

			// save the order in the gallery's meta
			update_post_meta($id, $this->meta_order, $order);
		}
		echo 'true'; 
		die();
	}


	// get the gallery's images
	function sthlm_get_current_gallery_images_ajax(){

		$id = (int) $_POST['id'];

		if($this->is_gallery($id)){
			$meta = get_post_meta($id, $this->meta_order, true);

			if(!empty($meta)){
				foreach ( (array) $meta as $m){
					$img = get_post($m);

					// echo the thumbs
					sthlm_img_thumb_template($img);

				} // end foreach
			}// end if
		}
		die();
	}



	function sthlm_gallery_query_attachments() {

		$args['post_type'] = 'attachment';
    	$args['post_mime_type'] = 'image';
    	$args['numberposts'] = -1;
    	$args['orderby'] = 'menu_order';
    	$args['order'] = 'ASC';

		// Query images by date
		if($_GET['context'] == 'wpsthlm-date'){
			$q = explode('/', $_GET['query']);
	    	if($q[0]){ $args['year'] = $q[0];}
	    	if($q[1]){ $args['monthnum'] = (int) $q[1];}
			$images = get_posts($args);

		// Query images by searchterm
		} elseif($_GET['context'] == 'wpsthlm-filter') {
			$q = $_GET['query'];
			$posts = array(); // skapa arrayen om den skulle vara tom vid implode vilket innebär error // SPATHON
			global $wpdb;


			/*
			 *
			 *   Rätt tabell prefix i databasen nu krävs att man har wp_
			 *  $wpdb->prefix = prefix
			 * $wpdb->posts = wp_posts
			 *
			 * Prepare för säkerheten
			 * %s = string
			 * %d = int
			 *
			 * // SPATHON
			 */

			$q = "%" . $q . "%";
			$res = $wpdb->get_results(
				$wpdb->prepare( "SELECT ID
					FROM $wpdb->posts
					WHERE post_excerpt LIKE '%s'
					OR post_title LIKE '%s'
					OR post_content LIKE '%s'
					AND post_type = 'attachment'" ,
					$q, $q, $q
				)
			);

			foreach($res as $r){
				$posts[] = $r->ID;
			};

			#echo '<pre>'; print_r($posts); echo '</pre>';
			#die();
			if(count($posts) == 0){
				die();
			}

			$args['include'] = implode($posts, ',');
			$images = get_posts($args);
		}

    	$i = 0;
    	// loop all images
    	if(!empty($images)): foreach ( (array) $images as $img ):

    	    // echo the thumbs
			sthlm_img_thumb_template($img); // templates.php

    	endforeach; else:

    	    _e('Inga bilder existerar', 'sthlm_gallery');

    	endif;
    	exit;

	}
	
	
	/* FLITER BY TAG */
	function sthlm_filter_by_tags_ajax() {


		$id = (int) $_POST['id'];

		$term = get_term( $id, 'img_cat' );

		//$arg = array('img_cat' => 8);
		$args = array(
			'img_cat' => $term->slug,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC'
		);

		$images = get_posts($args);
    	$i = 0;
    	// loop all images
    	if(!empty($images)): foreach ( (array) $images as $img ):

    	    // echo the thumbs
			sthlm_img_thumb_template($img); // templates.php

    	endforeach; else:

    	    _e('Inga bilder existerar', 'sthlm_gallery');

    	endif;
    	exit;

	}
	
	
	
	/*   EDIT IMG   */

	function sthlm_gallery_edit_image_data(){
		global $wp_taxonomies;
		$id = $_POST['id'];
		$title = $_POST['title'];
		$excerpt = $_POST['excerpt'];
		$content = $_POST['content'];
		$tags = $_POST['tags'];
		
		// Check if post is set
		if(!empty($id)){
		
			// Create post object
			$my_post = array(
				'ID' => $id,
				'post_title' => $title,
				'post_excerpt' => $excerpt,				
				'post_content' => $content,
				'post_type' => 'attachment',
				'post_status' => 'inherit'
			);

			// Insert the post into the database
			$pid = wp_update_post( $my_post );
			if($pid){
				//wp_set_post_terms( $pid, array('test','patrik'), 'img_cat' );
				// wp_set_object_terms( $pid, array('test','patrik'), 'img_cat' ) ERROR taxonomy don't exist

				wp_set_post_terms($pid, $tags, 'img_cat');
				/*
				echo '<pre>';
				print_r($wp_taxonomies);
				print_r(wp_set_object_terms($pid, array_map('trim', preg_split('/,+/', $tags)), 'img_cat', false));
				echo '<pre>';
				 */
			}
			echo $pid;
			//echo var_dump(get_post($pid));
		}
		die();		
	}


	/**
	 *
	 *    RANDOM FUNCTIONS
	 *
	 */

	// check if the id is of the post_type 'sthlm_gallery'
	function is_gallery($id){
		$gallery = get_post($id);
		if($gallery->post_type == 'sthlm_gallery'){
			return true;
		}else{
			return false;
		}
	}



}