<?php


if($_POST['form'] == 'add_gallery'):?>
	<!-- add gallery -->
	<form class="sthlm-gallery-cboxform" id="add_sthlm_gallery_form" action="" method="post">
		 <div>
			 <label class="sthlm-gallery-input-label">
				<strong>Titel</strong>
				<input type="text" id="sthlm_gallery_title_input" />
			 </label>
			 <label class="sthlm-gallery-textarea-label">
				 <strong>Beskrivning</strong>
				 <textarea id="sthlm_gallery_desc_textarea"></textarea>
			 </label>
			 <div>
				 <a href="#" class="button-secondary" id="sthlm_submit_new_gallery"><?php _e('Skapa galleriet', 'sthlm_gallery'); ?></a>
			 </div>
		</div>
	</form>
<?php elseif($_POST['form'] == 'edit_img'): ?>

	<!-- Edit image data -->
	<form class="sthlm-gallery-cboxform" id="sthlm-gallery-image-data" action="" method="post">
		 <div>
			 <img src="" alt="" id="sthlm_edit_preview">

			<?php
			//
			//   Ska man kunna ändra id? då kan man sriva över en existerande bild :S
			//
			?>
			<input type="text" readonly="readonly" name="sthlm-gallery-id" />
			 <label class="sthlm-gallery-input-label">
				<strong><?php _e('Titel', 'sthlm_gallery'); ?></strong>
				<input type="text" name="sthlm-gallery-title" />
			 </label>
			 <label class="sthlm-gallery-textarea-label">
				 <strong><?php _e('Beskrivning', 'sthlm_gallery'); ?></strong>
				 <textarea name="sthlm-gallery-excerpt"></textarea>
			 </label>
			 <label class="sthlm-gallery-textarea-label">
				 <strong><?php _e('Bildtext', 'sthlm_gallery'); ?></strong>
				 <textarea name="sthlm-gallery-content"></textarea>
			 </label>
			 <label class="sthlm-gallery-input-label">
				<strong><?php _e('Taggar', 'sthlm_gallery'); ?></strong>
				<input type="text" name="sthlm-gallery-tags" />
			 </label>
			 <div>
				 <a href="#" class="button-secondary" id="sthlm_save_img_settings"><?php _e('Spara', 'sthlm_gallery'); ?></a>
			 </div>
		</div>
	</form>

<?php else: ?>

	<?php _e('Något har gått fel.', 'sthlm_gallery'); ?>

<?php endif; ?>