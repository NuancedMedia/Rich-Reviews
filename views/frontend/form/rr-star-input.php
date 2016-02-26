<?php
?>

<tr class="rr_form_row">
	<td class="rr_form_heading rr_required"><?php echo $label; ?></td>
	<td class="rr_form_input">
		<span class="form-err<?php if ($error != '') { echo ' shown'; } ?>"><?php echo $error; ?></span>
		<div class="rr_stars_container">
			<span class="rr_star glyphicon glyphicon-star-empty" id="rr_star_1"></span>
			<span class="rr_star glyphicon glyphicon-star-empty" id="rr_star_2"></span>
			<span class="rr_star glyphicon glyphicon-star-empty" id="rr_star_3"></span>
			<span class="rr_star glyphicon glyphicon-star-empty" id="rr_star_4"></span>
			<span class="rr_star glyphicon glyphicon-star-empty" id="rr_star_5"></span>
		</div>
	</td>
</tr>

<?php render_custom_styles($options);

