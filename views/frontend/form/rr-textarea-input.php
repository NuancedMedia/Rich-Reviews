<?php
?>
	<tr class="rr_form_row">
		<td class="rr_form_heading <?php if($require){ echo 'rr_required'; } ?>">
			<?php echo $label; ?>
		</td>
		<td class="rr_form_input">
			<?php echo $error; ?>
			<textarea class="rr_large_input" name="rText" rows="10"><?php echo $rText; ?></textarea>
		</td>
	</tr>
