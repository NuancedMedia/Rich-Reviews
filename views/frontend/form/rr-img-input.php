<?php
?>

	<tr class="rr_form_row">
		<td class="rr_form_heading <?php if($require){ echo 'rr_required'; } ?>" >
			<?php echo $label; ?>
		</td>
		<td class="rr_form_input">
			<span class="form-err<?php if ($error != '') { echo ' shown'; } ?>"><?php echo $error; ?></span>
			<input type="text" name="rrInsertReviewerImageDisplay" id="rrInsertReviewerImageDisplay" class="rr_small_input" readonly/>
			<input type="file" name="rrInsertReviewerImageFile" size="1" id="rrInsertReviewerImageFile" style="visibility:hidden;height:0px;" />
			<button type="button" class="front_upload_image_button" style="margin-top:0;font-size:70%;padding:3px 5px;">Upload/Update Image</button>
		</td>


		<script type="text/javascript">
			jQuery(function() {
				jQuery('#<?php echo $options['unique_key']; ?>').find('.front_upload_image_button').click(function( event ){
					jQuery('#<?php echo $options['unique_key']; ?>').find('#rrInsertReviewerImageFile').click();
				});
				jQuery('#<?php echo $options['unique_key']; ?>').find('#rrInsertReviewerImageFile').change(function(event) {
					files = jQuery('#rrInsertReviewerImageFile').get(0).files;
					filesString = '';
					numFiles = files.length;
					count = 0;
					jQuery.each(files, function() {
						count++;
						if(count == numFiles) {
							filesString += this.name;
						} else {
							filesString += this.name + ', ';
						}
					});
					jQuery('#<?php echo $options['unique_key']; ?>').find('#rrInsertReviewerImageDisplay').val(filesString);
				});
			});
		</script>
	</tr>
