/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function($){


	var $aGallery = $('.sthlm-gallery'), // all gellery items
		$currGallerySettings = $('#sthlm_current_gallery_images_settings'), // wrapper for curr gallery images save button
		$currImages = $('#sthlm_current_gallery_images'), // wrapper for the gallery images
		$currImgContainer = $('#sthlm_current_gallery_images'), // the wrapper for the curr gallery images
		$allImagesContainer = $('#sthlm_all_images'), // the wrapper for all images
		dropState = false, // used for drag/drop on init?
		dummy = 'var';




	// Add class to all img in the current gallery
	var imgInGallery = function(){
		var imagesString = $currImages.attr('data-gallery-thumbs'),
			images = imagesString.split(','),
			numImg = (images.length - 1);
		for(var i = 0; i <= numImg;i++){
			$allImagesContainer.find(".sthlm-thumb[data-thumb-id='"+ images[i] +"']").addClass('sthlm-thumb-exist');
		}
	}

	// prevent manual submit
	$('#sthlm_lightbox').find('form').live('submit', function(){return false;});
	
	
	
	
	
	/**
	 *
	 *   ADD
	 *
	 */
	// load and show the form
	$('#sthlm_add_gallery_button').click(function(){
		// set post data
		var data = {
			action: 'sthlm_load_lightbox_form_ajax',
			form:  'add_gallery'
		};
		$.post(ajaxurl, data, function(response) {

			$('#sthlm_lightbox').html(response);
			$.colorbox({inline:true, href:"#sthlm_lightbox"});
		});
	});

	// Save the gallery
	$('#sthlm_submit_new_gallery').live('click', function(){

		var $this = $(this),
			title = $('#sthlm_gallery_title_input').val(),
			desc = $('#sthlm_gallery_desc_textarea').val();

		// no title
		if(title === ''){
			alert('No title');
			return false;
		}


		// set post data
		var data = {
			action: 'sthlm_gallery_ajax_add',
			title:  title,
			desc: desc
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			$('#sthlm_galleries').prepend(response);
			$.colorbox.close();
		});
		return false;
	});


	
	/**
	 *
	 *   Select gallery
	 *
	 */
	$aGallery.live('click', function(e){

		// stop click on remove to fire an event
		if($(e.target).is('.remove-sthlm-gallery') ) {return;}

		var $this = $(this),
			dataID = $this.attr('data-gallery-id');

		// remove slected from all galleries and add selected to this gallery
		$aGallery.removeClass('selected-sthlm-gallery');
		$this.addClass('selected-sthlm-gallery');
		$currGallerySettings.attr('data-gallery-id', dataID);


		// set post data
		var data = {
			action: 'sthlm_get_current_gallery_images_ajax',
			id: dataID
		};
		$.post(ajaxurl, data, function(response) {
			$currImages.html(response);


			// remove class for used images
			$allImagesContainer.find('.sthlm-thumb').removeClass('sthlm-thumb-exist');

			// Put all img id's in an array
			var id = '',
				i = 0;
			$currImages.find('.sthlm-thumb').each(function(){
				var thumbID = $(this).attr('data-thumb-id');
				if(i === 0){
					id += thumbID;
				}else{
					id += ','+thumbID;
				}
				
				$allImagesContainer.find(".sthlm-thumb[data-thumb-id='"+thumbID+"']").addClass('sthlm-thumb-exist');
				i++;
			});


			$currImages.attr('data-gallery-thumbs', id);

		});
	});
	
	/**
	 *
	 *   DELETE GALLERY
	 *
	 */
	$('.remove-sthlm-gallery').live('dblclick', function(){


		var $this = $(this),
			$gallery = $this.parent(),
			dataID = $gallery.attr('data-id');

		// set post data
		var data = {
			action: 'sthlm_delete_gallery_ajax',
			id: dataID
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response === 'error'){
				alert('Please try again!');
			}else{
				// remove the object
				$gallery.remove();
			}
		});
		return false;
	});
	
	
	
	
	
	/*
	 *
	 *   UPDATE GALLERY IMAGES/ORDER
	 *
	 */
	$('#sthlm_uppdate_gallery_images').click(function(){
		var $this = $(this),
			$loader = $this.find('.sthlm-gallery-loader'),
			order = [],
			id = $currGallerySettings.attr('data-gallery-id'), // data-gallery-id
			i = 0;

		// show loader
		$loader.removeClass('hidden');

		// get all images in order and there id
		$('#sthlm_current_gallery_images .sthlm-thumb').each(function(){
			order[i] = $(this).attr('data-thumb-id');
			i++;
		});

		// set post data
		var data = {
			action: 'sthlm_gallery_ajax_save_order',
			order: order,
			id: id
		};
		$.post(ajaxurl, data, function(response) {
			// hide loader
			$loader.addClass('hidden');
		});
		return false;
	});
	

	/**
	 *
	 *  Remove img
	 *
	 */
	$('.remove-sthlm-thumb').live('click', function(){
		var $this = $(this),
			$thumb = $this.closest('.sthlm-thumb'),
			thumbID = $thumb.attr('data-thumb-id');
		$thumb.remove();
		$allImagesContainer.find(".sthlm-thumb[data-thumb-id='"+thumbID+"']").removeClass('sthlm-thumb-exist');
	});






	/*
	 *
	 *   SORTER
	 *
	 *
     */
	var sthlmDragDrop = function(){
		//$('.sthlm-images-container .sthlm-thumb').draggable({
		$allImagesContainer.find('.sthlm-thumb').not('.sthlm-thumb-exist').draggable({
			//appendTo: ".sthlm-images-container",
			helper: "clone",
			opacity: 1,
			cancel: '.sthlm-thumb-exist',
			disabled: dropState/*,
			start: function(event, ui){
				var data = ui.helper.attr('data-thumb-id');
				if($currImgContainer.find("div[data-thumb-id='"+data+"']").length != 0){
					$(this).addClass('sthlm-img-exist');
				}
			},
			stop: function(event, ui){
				$(this).removeClass('sthlm-img-exist');
			}*/
		});
	}
	sthlmDragDrop();
	$currImgContainer.droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.ui-sortable-helper)",
		drop: function( event, ui ) {
			var $this = $(this);

			// clone the img to the gallery
			$this.append(ui.draggable.clone());

			// add class selected to the img
			ui.draggable.addClass('sthlm-thumb-exist');

		}
	});

	$currImgContainer.sortable({
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		items: '.sthlm-thumb',
		helper: 'orginal'//'clone'
	}).disableSelection();





	/*
	 *
	 *   FILTER AND SUCH
	 *
	 */

	 $('#sthlm-gallery-datepicker select, #sthlm-gallery-filter input').change( function(){
	 	var data = {
	 		query: $(this).val(),
	 		context: $(this).attr('id'),
	 		action: 'sthlm_gallery_query_attachments'
	 	}
	 	$.get(ajaxurl, data, function(data) {
			$allImagesContainer.html(data);
			sthlmDragDrop();
			imgInGallery();
		});
	 });









	/**
	 *
	 *   EDIT IMG
	 *
	 */

	// Open Cbox to edit image data
	$('.sthlm-edit-thumb-info').live('click', function(){
		// save for performance and this as value futher down
		var $this = $(this),
			$infoBox = $this.parent().find('.sthlm-thumb-data');


		// set post data
		var data = {
			action: 'sthlm_load_lightbox_form_ajax',
			form:  'edit_img'
		};
		$.post(ajaxurl, data, function(response) {

			$('#sthlm_lightbox').html(response);
			$.colorbox({inline:true, href:"#sthlm_lightbox"}, function(){
				$('#sthlm_edit_preview').attr('src', $this.parent().find('img').attr('src'));
				$('input[name="sthlm-gallery-id"]').val($infoBox.find('.sthlm-thumb-id').text());
				$('input[name="sthlm-gallery-title"]').val($infoBox.find('.sthlm-thumb-title').text());
				$('textarea[name="sthlm-gallery-excerpt"]').val($infoBox.find('.sthlm-thumb-excerpt').text());
				$('textarea[name="sthlm-gallery-content"]').val($infoBox.find('.sthlm-thumb-content').text());
			});
		});
	});

	// save settings
	$('#sthlm_save_img_settings').live('click', function(){
		var id = $('input[name="sthlm-gallery-id"]').val(),
			title = $('input[name="sthlm-gallery-title"]').val(),
			excerpt = $('textarea[name="sthlm-gallery-excerpt"]').val(),
			content = $('textarea[name="sthlm-gallery-content"]').val();
			
		// set post data
		var data = {
			action: 'sthlm_gallery_edit_image_data',
			id: id,
			title: title,
			excerpt: excerpt,
			content: content
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			//console.log(response)
			//$('#gallery_dir_wrapper').prepend(response);
			var $infoBox = $('.sthlm-thumb-'+ id).find('.sthlm-thumb-data');
			//console.log($infoBox);
			// update the data
			$infoBox.find('.sthlm-thumb-title').text(title);
			$infoBox.find('.sthlm-thumb-excerpt').text(excerpt);
			$infoBox.find('.sthlm-thumb-content').text(content);
			
			$.colorbox.close();
		});
		return false;
	});


















	/*
	var dropState = true; // prevent drag n drop if no gallery is set



	// all objects
	var $galleryImages = $('#images_in_sthlm_gallery_wrapper'), // the wrapper div for gallery images -> .sthlm-gallery-inside
		$selectedImgWrap = $galleryImages.find('.sthlm-gallery-inside'),
		$allImagesWrapp = $('.sthlm-gallery-inside-all'),
		$gallery = $('.sthlm-gallery-folder');
	// sort the thumbs
	var $thumb = $('.sthlm-thumb'),
		$placeholder = $('.sthlm-gallery-inside');
	
	




	/**
	 *
	 *   Select gallery/folder
	 *
	 *
	$gallery.live('click', function(e){

		// stop click on remove to fire an event
		if($(e.target).is('.remove-sthlm-gallery') ) { return; }

		var $this = $(this),
			dataID = $this.attr('data-id');

		$('.sthlm-gallery-folder').removeClass('selected-sthlm-gallery');
		$this.addClass('selected-sthlm-gallery');
		$galleryImages.attr('data-id', dataID);


		// set post data
		var data = {
			action: 'sthlm_gallery_ajax_folder_chosen',
			id: dataID
		};
		$.post(ajaxurl, data, function(response) {
			$selectedImgWrap.html(response);
		});

		// sort fields
		$('.sthlm-thumb').draggable( "option", "disabled", false );
		dropState = false;
	});





	/*
	 *
	 *   SORTER
	 *
	 *
     *
	var sthlmDragDrop = function(){
		$('.sthlm-gallery-inside-all .sthlm-thumb').draggable({
			appendTo: ".sthlm-gallery-inside-all",
			helper: "clone",
			opacity: 1,
			disabled: dropState
		});
	}
	sthlmDragDrop();
	$('.sthlm-gallery-inside-current').droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.ui-sortable-helper)",
		drop: function( event, ui ) {
			var $this = $(this),
				data = ui.draggable.attr('data-thumb-id');

			// if not allready exist
			if($this.find("div[data-thumb-id='"+data+"']").length === 0){
				$(this).append(ui.draggable.clone());
			}else{
				//alert('Bilden finns redan');
			}
		}
	});

	$('.sthlm-gallery-inside-current').sortable({
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		items: '.sthlm-thumb',
		helper: 'clone'
	}).disableSelection();
	


	/*
	 *
	 *   UPDATE GALLERY IMAGES/ORDER
	 *
	 *

	$('#uppdate_sthlm_gallery_images').click(function(){
		var $this = $(this),
			$loader = $this.find('.sthlm-gallery-loader'),
			order = new Array,
			id = $('#images_in_sthlm_gallery_wrapper').attr('data-id'), // data-gallery-id
			i = 0;

		// show loader
		$loader.removeClass('hidden');

		// get all images in order and there id
		$('#images_in_sthlm_gallery_wrapper .sthlm-thumb').each(function(){
			order[i] = $(this).attr('data-thumb-id');
			i++;
		});

		// set post data
		var data = {
			action: 'sthlm_gallery_ajax_save_order',
			order: order,
			id: id
		};
		$.post(ajaxurl, data, function(response) {
			// hide loader
			$loader.addClass('hidden');
		});
		return false;
	});






	/**
	 *
	 *  Remove img
	 *
	 *
	$('.remove-sthlm-thumb').live('click', function(){
		$(this).closest('.sthlm-thumb').remove();
	});







	// equal height on the columns
	var gallery_box_height = 0;
	$('#wrapp_all_sthlm_images  .sthlm-gallery-inside').each(function(){
		var box_height = $(this).height();
		if(box_height > gallery_box_height) gallery_box_height = box_height;
		//console.log(gallery_box_height);
	}).css({"min-height": gallery_box_height+'px'});










	/*
	 *
	 *   ADD A GALLERY
	 * 
	 *
	// open lightbox to add gallery
	$('#add_sthlm_gallery_button').colorbox({inline:true});

	// prevent manual submit
	$('#add_sthlm_gallery_form').submit(function(){ return false; });

	//add gallery
	$('#submit_new_sthlm_gallery').click(function(){

		var $this = $(this),
			title = $('#sthlm_gallery_title_input').val(),
			desc = $('#sthlm_gallery_desc_textarea').val();

		// no title
		if(title === ''){
			alert('No title');
			return false;
		}


		// set post data
		var data = {
			action: 'sthlm_gallery_ajax_add',
			title:  title,
			desc: desc
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {

			$('#gallery_dir_wrapper').prepend(response);
			$('#add_sthlm_gallery_button').colorbox.close();
		});
		return false;
	});



	/**
	 *
	 *   DELETE GALLERY
	 *
	 *
	$('.remove-sthlm-gallery').live('dblclick', function(){


		var $this = $(this),
			$gallery = $this.parent(),
			dataID = $gallery.attr('data-id');

		// set post data
		var data = {
			action: 'sthlm_gallery_ajax_delete',
			id: dataID
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response === 'error'){
				alert('Please try again!');
			}else{
				// remove the object
				$gallery.remove();
			}
		});
		return false;
	});






	
	/*
	 *
	 *   FILTER AND SUCH
	 *
	 *
	 
	 $('#sthlm-gallery-datepicker select, #sthlm-gallery-filter input').change( function(){

	 	var data = {
	 		query: $(this).val(),
	 		context: $(this).attr('id'),
	 		action: 'sthlm_gallery_query_attachments'
	 	}

	 	$.get(ajaxurl, data, function(data) {
			$allImagesWrapp.html(data);
			sthlmDragDrop();
			editImageData();
		});
	 });

	 

	 /*
	  *
	  *  Show and hide
	  *
	  *
	 $('.sthlm-thumb').live('mouseover mouseout', function(event) {
		if (event.type == 'mouseover') {
			$('.sthlm-thumb-data', this).show();	
		} else {
			$('.sthlm-thumb-data', this).hide();	 
		}
	});

	 /*
	  *
	  *  Edit Image data
	  *
	  *

	var editImageData = function(){
	
		// Open Cbox to edit image data
		$('.sthlm-gallery-edit-image-data').live('click', function(){
			// save for performance and this as value futher down
			var $this = $(this);
			$.fn.colorbox({inline:true, href: $this.attr('href')}, function(){
				// Populate fields in the callback

				$('#sthlm_edit_preview').attr('src', $this.parent().parent().find('img').attr('src'));
				$('input[name="sthlm-gallery-id"]').val($this.siblings('.sthlm-thumb-id').text());
				$('input[name="sthlm-gallery-title"]').val($this.siblings('.sthlm-thumb-title').text());
				$('textarea[name="sthlm-gallery-excerpt"]').val($this.siblings('.sthlm-thumb-excerpt').text());
				$('textarea[name="sthlm-gallery-content"]').val($this.siblings('.sthlm-thumb-content').text());

			});
		});
	
		// Prevent manual submit
		$('#sthlm-gallery-image-data').submit(function(){ return false; });
	
		//add gallery
		$('#submit-sthlm-gallery-image-data').click(function(){
	
			// set post data
			var data = {
				action: 'sthlm_gallery_edit_image_data',
				id: $('input[name="sthlm-gallery-id"]').val(),			
				title: $('input[name="sthlm-gallery-title"]').val(),
				excerpt: $('textarea[name="sthlm-gallery-excerpt"]').val(),
				content: $('textarea[name="sthlm-gallery-content"]').val()
			};
			
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
	
				//console.log(response)
				//$('#gallery_dir_wrapper').prepend(response);
				$('#add_sthlm_gallery_button').colorbox.close();
			});
			return false;
		 });
	}
	editImageData();
	*/
});