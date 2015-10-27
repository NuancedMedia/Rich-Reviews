<?php
?>

	<tr class="rr_form_row">
		<td class="rr_form_heading <?php if($require){ echo 'rr_required'; } ?>" >
			<?php echo $label; ?>
		</td>
		<td class="rr_form_input">
			<?php echo '<span class="form-err">' . $error . '</span>'; ?>
			<input type="text" name="rrInsertReviewerImageDisplay" id="rrInsertReviewerImageDisplay" class="rr_small_input" readonly/>
			<input type="file" name="rrInsertReviewerImageFile" size="1" id="rrInsertReviewerImageFile" style="visibility:hidden;height:0px;" />
			<button type="button" class="front_upload_image_button" style="margin-top:0;font-size:70%;padding:3px 5px;">Upload/Update Image</button>
		</td>


		<script type="text/javascript">
			jQuery('.front_upload_image_button').live('click', function( event ){
				console.log("hahahahah");
				jQuery('#rrInsertReviewerImageFile').click();
			});
			jQuery('#rrInsertReviewerImageFile').change(function() {
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
				jQuery('#rrInsertReviewerImageDisplay').val(filesString);
			});
		</script>
	</tr>
