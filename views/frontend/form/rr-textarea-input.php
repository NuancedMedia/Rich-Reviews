<?php
?>
	<tr class="rr_form_row">
		<td class="rr_form_heading <?php if($require){ echo 'rr_required'; } ?>">
			<?php echo $label; ?>
		</td>
		<td class="rr_form_input">
			<span class="form-err<?php if ($error != '') { echo ' shown'; } ?>"><?php echo $error; ?></span>
			<textarea class="rr_large_input" name="rText" rows="10"><?php echo $rFieldValue; ?></textarea>
		</td>
	</tr>
