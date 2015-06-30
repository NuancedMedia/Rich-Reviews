<?php

function handle_form($atts, $options, $sqltable, $path) {
	global $wpdb;
	global $post;
	extract(shortcode_atts(
		array(
			'category' => 'none',
		)
	,$atts));

	//initialize all data vars
	$rName  = '';
	$rEmail = '';
	$rTitle = '';
	$rText  = '';
	// $nameErr = '';
	// $emailErr = '';
	// $titleErr = '';
	// $reviewErr = '';
	// $textErr = '';
	$output = '';
	$displayForm = true;
	$posted = false;
	$errors = array(
		'name'	=>	'',
		'email'	=>	'',
		'title'	=>	'',
		'rating'	=>	'',
		'content'=>	''
	);


	$newData = array(
		'reviewer_name'   => $rName,
		// 'reviewer_image_id' => $rAuthorImage,
		'reviewer_email'  => $rEmail,
		'review_title'    => $rTitle,
		// 'review_rating'   => intval($rRating),
		// 'review_image_id' => $rImage,
		'review_text'     => $rText,
		'errors'		  => $errors
	);

	if (isset($_POST['submitted'])) {
		if ($_POST['submitted'] == 'Y') {

			$posted = true;

			$incomingData = $_POST;
			$incomingData = apply_filters('rr_process_form_data', $incomingData);

			if ($options['form-name-display']) {
				$rName     = fp_sanitize($_POST['rName']);
			}
			// if ($options['form-reviewer-image-display']) {
			// 	$imageId = media_handle_upload('rAuthorImage',0);
			// 	$rAuthorImage = $imageId;
			// 	dump($rAuthorImage);
			// }
			if ($options['form-email-display']) {
				$rEmail    = fp_sanitize($_POST['rEmail']);
			}
			if ($options['form-title-display']) {
				$rTitle    = fp_sanitize($_POST['rTitle']);
			}
			$rRating   = fp_sanitize($_POST['rRating']);
			// if ($options['form-reviewed-image-display']) {
			// 	$imageId = media_handle_upload('rImage',0);
			// 	$rImage = $imageId;
			// 	dump($rImage);
			// }
			if ($options['form-content-display']) {
				$rText     = fp_sanitize($_POST['rText']);
			}

			$rDateTime = date('Y-m-d H:i:s');
			$incomingData['rDateTime'] = $rDateTime;
			if ($options['require_approval']) {$rStatus   = 0;} else {$rStatus   = 1;}
			$rIP       = $_SERVER['REMOTE_ADDR'];
			$rPostID   = $post->ID;
			$rCategory = fp_sanitize($category);

			// $errors = array(
			// 	'nameErr'	=>	'',
			// 	'emailErr'	=>	'',
			// 	'titleErr'	=>	'',
			// 	'ratingErr'	=>	'',
			// 	'contentErr'=>	''
			// );

			$newData = array (

					'date_time'       => $rDateTime,
					'reviewer_name'   => $rName,
					// 'reviewer_image_id' => $rAuthorImage,
					'reviewer_email'  => $rEmail,
					'review_title'    => $rTitle,
					'review_rating'   => intval($rRating),
					// 'review_image_id' => $rImage,
					'review_text'     => $rText,
					'review_status'   => $rStatus,
					'reviewer_ip'     => $rIP,
					'post_id'		  => $rPostID,
					'review_category' => $rCategory,
					'isValid'		  => true,
					'errors'		  => $errors

			);


			$newData = apply_filters('rr_check_required', $newData);
			if ($newData['isValid']) {
				$newData = apply_filters('rr_misc_validation', $newData);
				dump($newData);
			}
			if ($newData['isValid']) {

				$displayForm = false;
				$newSubmission = array(
					'date_time'       => $newData['date_time'],
					'reviewer_name'   => $newData['reviewer_name'],
					// 'reviewer_image_id' => $newData['reviewer_image_id'],
					'reviewer_email'  => $newData['reviewer_email'],
					'review_title'    => $newData['review_title'],
					'review_rating'   => intval($newData['rating']),
					// 'review_image_id' => $newData['review_image_id'],
					'review_text'     => $newData['review_text'],
					'review_status'   => $newData['review_status'],
					'reviewer_ip'     => $newData['reviewer_ip'],
					'post_id'		  => $newData['post_id'],
					'review_category' => $newData['review_category'],
				);

				do_action('rr_on_valid_data', $newSubmission, $options, $sqltable);
			}


			// $validData = true;
			// if($options['form-name-display']) {
			// 	if($options['form-name-require']) {
			// 		if ($rName == '') {
			// 		$nameErr = '<span class="form-err">' . __('You must include your name.', 'rich-reviews') . '</span><br>';
			// 		$validData = false;
			// 		}
			// 	}
			// }
			// if($options['form-title-display']) {
			// 	if($options['form-title-require']) {
			// 		if ($rTitle == '') {
			// 			$titleErr= '<span class="form-err">' . __('You must include a title for your review.', 'rich-reviews') . '</span><br>';
			// 			$validData = false;
			// 		}
			// 	}
			// }
			// if($options['form-content-display']) {
			// 	if($options['form-content-require']) {
			// 		if ($rText == '') {
			// 			$textErr = '<span class="form-err">' . __('You must write some text in your review.', 'rich-reviews') . '</span><br>';
			// 			$validData = false;
			// 		}
			// 	}
			// }

			// if ($rRating == 0) {
			// 	$reviewErr = '<span class="form-err">' . __('Please give a rating between 1 and 5 stars.', 'rich-reviews') . '</span><br>';
			// 	$validData = false;
			// }
			// if($options['form-email-display']) {
			// 	if($options['form-email-require']) {
			// 		if($rEmail == '') {
			// 			$emailErr = '<span class="form-err">' . __('Please provide email.', 'rich-reviews') . '</span><br>';
			// 		}
			// 	}
			// 	if ($rEmail != '') {
			// 		$firstAtPos = strpos($rEmail,'@');
			// 		$periodPos  = strpos($rEmail,'.');
			// 		$lastAtPos  = strrpos($rEmail,'@');
			// 		if (($firstAtPos === false) || ($firstAtPos != $lastAtPos) || ($periodPos === false)) {
			// 			$emailErr .= '<span class="form-err">' . __('You must provide a valid email address.', 'rich-reviews') . '</span><br>';
			// 			$validData = false;
			// 		}
			// 	}
			// }


				// if( $options['send-email-notifications']) {
				// 	sendEmail($newdata, $options);
				// }

				// $output .= '<span id="state"></span>';

				// //TODO: format for i18n
				// $output .= '<div class="successful"><span class="rr_star glyphicon glyphicon-star left" style="font-size: 34px;"></span><span class="rr_star glyphicon glyphicon-star big-star right" style="font-size: 34px;"></span><center><strong>' . $rName . ', your review has been recorded';
				// if($options['require_approval']) {
				// 	$output .= ' and submitted for approval';
				// }
				// $output .= '. Thanks!</strong></center><div class="clear"></div></div>';
				// $displayForm = false;
		// 	} else {
		// 		//$output .= '<span id="target"></span>';
		// 	}
		// }

		}
	} else {
		?> <span id="state"></span> <?php
	}
	if ($displayForm) {
		// if(!$posted) {
		// 	$newdata = array(
		// 		'date_time'       => $rDateTime,
		// 		'reviewer_name'   => $rName,
		// 		// 'reviewer_image_id' => $rAuthorImage,
		// 		'reviewer_email'  => $rEmail,
		// 		'review_title'    => $rTitle,
		// 		'review_rating'   => intval($rRating),
		// 		// 'review_image_id' => $rImage,
		// 		'review_text'     => $rText,
		// 		'review_status'   => $rStatus,
		// 		'reviewer_ip'     => $rIP,
		// 		'post_id'		  => $rPostID,
		// 		'review_category' => $rCategory
		// 	);
		// }


			$errors = $newData['errors'];
			$errors = generate_error_text($errors);
			dump($errors);

		?>
		<form action="" method="post" enctype="multipart/form-data" class="rr_review_form" id="fprr_review_form">
			<input type="hidden" name="submitted" value="Y" />
			<input type="hidden" name="rRating" id="rRating" value="0" />
			<table class="form_table">
			<?php do_action('rr_do_form_fields', $options, $path, $newData, $errors); ?>
		<?php

	// 	if($options['form-name-display']) {
	// 		$output .= '		<tr class="rr_form_row">';
	// 		$output .= '			<td class="rr_form_heading';
	// 		if($options['form-name-require']){
	// 			$output .= ' rr_required';
	// 		}
	// 		$output .= '">'.$options['form-name-label'].'</td>';
	// 		$output .= '			<td class="rr_form_input">'.$nameErr.'<input class="rr_small_input" type="text" name="rName" value="' . $rName . '" /></td>';
	// 		$output .= '		</tr>';
	// 	}
	// 	// if($options['form-reviewer-image-display']) {
	// 	// 	$output .= '	 <tr class="rr_form_row">';
	// 	// 	$output .= '		<td class="rr_form_heading';
	// 	// 	if($options['form-reviewer-image-require']) {
	// 	// 		$output .= ' rr_required';
	// 	// 	}
	// 	// 	$output .= ' ">'.$options['form-reviewer-image-label']. '</td>';
	// 	// 	$output .= '			<td class="rr_form_input">'.$textErr.'<input type="file" name="rAuthorImage" size="50"/></td>';
	// 	// 	$output .= '		</tr>';
	// 	// }

	// 	if($options['form-email-display']) {
	// 		$output .= '		<tr class="rr_form_row">';
	// 		$output .= '			<td class="rr_form_heading';
	// 		if($options['form-email-require']){
	// 			$output .= ' rr_required';
	// 		}
	// 		$output .= '">'.$options['form-email-label'].'</td>';
	// 		$output .= '			<td class="rr_form_input">'.$emailErr.'<input class="rr_small_input" type="text" name="rEmail" value="' . $rEmail . '" /></td>';
	// 		$output .= '		</tr>';
	// 	}

	// 	if($options['form-title-display']) {
	// 		$output .= '		<tr class="rr_form_row">';
	// 		$output .= '			<td class="rr_form_heading';
	// 		if($options['form-title-require']){
	// 			$output .= ' rr_required';
	// 		}
	// 		$output .= '">'.$options['form-title-label'].'</td>';
	// 		$output .= '			<td class="rr_form_input">'.$titleErr.'<input class="rr_small_input" type="text" name="rTitle" value="' . $rTitle . '" /></td>';
	// 		$output .= '		</tr>';
	// 	}

	// 	$output .= '		<tr class="rr_form_row">';
	// 	$output .= '			<td class="rr_form_heading rr_required">Rating</td>';
	// 	$output .= '			<td class="rr_form_input">'.$reviewErr . star_rating_input() . '</td>';
	// 	$output .= '		</tr>';

	// 	//TODO: Maybe immplement array of images
	// 	// if($options['form-reviewed-image-display']) {
	// 	// 	$output .= '	 <tr class="rr_form_row">';
	// 	// 	$output .= '		<td class="rr_form_heading';
	// 	// 	if($options['form-reviewed-image-require']) {
	// 	// 		$output .= ' rr_required';
	// 	// 	}
	// 	// 	$output .= ' ">'.$options['form-reviewed-image-label']. '</td>';
	// 	// 	$output .= '			<td class="rr_form_input">'.$textErr.'<input type="file" name="rImage" size="50"/></td>';
	// 	// 	$output .= '		</tr>';
	// 	// }

	// 	if($options['form-content-display']) {
	// 		$output .= '		<tr class="rr_form_row">';
	// 		$output .= '			<td class="rr_form_heading';
	// 		if($options['form-content-require']) {
	// 			$output .= ' rr_required';
	// 		}
	// 		$output .= '">'.$options['form-content-label'].'</td>';
	// 		$output .= '			<td class="rr_form_input">'.$textErr.'<textarea class="rr_large_input" name="rText" rows="10">' . $rText . '</textarea></td>';
	// 		$output .= '		</tr>';
	// 	}

	?>
				<tr class="rr_form_row">
					<td></td>
					<td class="rr_form_input"><input id="submitReview" name="submitButton" type="submit" value="<?php echo $options['form-submit-text']; ?>"/></td>
				</tr>
			</table>
		</form>
	<?php
	// }
	// render_custom_styles($options);
	// if( $options['return-to-form']) {
	// 		$output .= '<script>
	// 						jQuery(function(){
	// 							if(jQuery(".successful").is(":visible")) {
	// 								offset = jQuery(".successful").offset();
	// 								jQuery("html, body").animate({
	// 									scrollTop: (offset.top - 400)
	// 								});
	// 							} else {
	// 								if(jQuery(".form-err").is(":visible")) {
	// 									offset = jQuery(".form-err").offset();
	// 									jQuery("html, body").animate({
	// 										scrollTop: (offset.top - 200)
	// 									});
	// 								}
	// 							}
	// 						});
	// 					</script>';
	// }
	// return __($output, 'rich-reviews');
	}
}
function generate_error_text($errors) {
	dump("shats");
	dump($errors);
	$processed = array();
	foreach($errors as $key => $val) {
		if($val == 'absent required') {
			$processed[$key] = 'The ' . $key . ' field is required.';
		} else if ($val == 'invalid input') {
			$processed[$key] = 'Please enter a valid ' . $key;
		} else if ($val == 'length violation') {
			$processed[$key] = 'The ' . $key . ' that you entered is too long.';
		} else {
			$processed[$key] = '';
		}
	}
	return $processed;
}

function sanitize_incoming_data($incomingData) {

	$modifiedData = array();
	foreach($incomingData as $field => $val) {
		$incomingData[$field] = fp_sanitize($val);
	}
	// $incomingData = $modifiedData;
	return $incomingData;
}

function rr_do_rating_field($options, $path, $rData = null, $errors = null) {
	$error = $errors['rating'];

	@include $path . 'views/frontend/form/rr-star-input.php';

}

function render_custom_styles($options) {
	?>
	<style>
		.stars, .rr_star {
			color: <?php echo $options['star_color']?>;
		}
	</style>
	<?php
}

function rr_do_name_field($options, $path, $rData = null, $errors = null) {
	$inputId = 'Name';
	$require = false;
	$rFieldValue = '';
	$error = $errors['name'];
	$label = $options['form-name-label'];
	if($options['form-name-require']) {
		$require = true;
	}
	if($rData['reviewer_name']) {
		$rFieldValue = $rData['reviewer_name'];
	}

	@include $path . 'views/frontend/form/rr-text-input.php';
}

function rr_do_email_field($options, $path, $rData = null, $errors = null) {
	$inputId = 'Email';
	$require = false;
	$rFieldValue = '';
	$error = $errors['email'];
	$label = $options['form-email-label'];
	if($options['form-email-require']) {
		$require = true;
	}
	if($rData['reviewer_email']) {
		$rFieldValue = $rData['reviewer_email'];
	}

	@include $path . 'views/frontend/form/rr-text-input.php';
}

function rr_do_title_field($options, $path, $rData = null, $errors = null) {
	$inputId = 'Title';
	$require = false;
	$rFieldValue = '';
	$error = $errors['title'];
	$label = $options['form-title-label'];
	if($options['form-title-require']) {
		$require = true;
	}
	if($rData['review_title']) {
		$rFieldValue = $rData['review_title'];
	}

	@include $path . 'views/frontend/form/rr-text-input.php';
}

function rr_do_content_field($options, $path, $rData = null, $errors = null) {
	$inputId = 'Text';
	$require = false;
	$rFieldValue = '';
	$error = $errors['content'];
	$label = $options['form-content-label'];
	if($options['form-content-require']) {
		$require = true;
	}
	if($rData['review_text']) {

		$rFieldValue = $rData['review_text'];
	}
	@include $path . 'views/frontend/form/rr-textarea-input.php';
}

function rr_insert_new_review($data, $options, $sqltable) {

	global $wpdb;
	$wpdb->insert($sqltable, $data);
}

function rr_output_response_message() {
	// //TODO: format for i18n
				// $output .= '<div class="successful"><span class="rr_star glyphicon glyphicon-star left" style="font-size: 34px;"></span><span class="rr_star glyphicon glyphicon-star big-star right" style="font-size: 34px;"></span><center><strong>' . $rName . ', your review has been recorded';
				// if($options['require_approval']) {
				// 	$output .= ' and submitted for approval';
				// }
				// $output .= '. Thanks!</strong></center><div class="clear"></div></div>';
}

function rr_send_admin_email($data, $options) {

	extract($data);
	$message = "";
	$message .= "RichReviews User,\r\n";
	$message .= "\r\n";
	$message .= __("You have received a new review which is now pending your approval. The information from the review is listed below.", 'rich-reviews') . "\r\n";
	$message .= "\r\n";
	$message .= __("Review Date: ", 'rich-reviews') .$date_time."\r\n";
	if( $reviewer_name != "" ) {
		$message .= $options["form-name-label"].": ".$reviewer_name."\r\n";
	}
	if ( $reviewer_email != "" ) {
		$message .= $options["form-email-label"].": ".$reviewer_email."\r\n";
	}
	if ( $review_title != "" ) {
		$message .= $options["form-title-label"].": ".$review_title."\r\n";
	}
	$message .= __("Review Rating: ", 'rich-reviews'). $review_rating ."\r\n";
	if ($review_text != "" ) {
		$message .= $options["form-content-label"].": ".$review_text."\r\n";
	}
	$message .= __("Review Category: ", 'rich-reviews') . $review_category ."\r\n\r\n";

	$message .= __("Click the link below to review and approve your new review.", 'rich-reviews'). "\r\n";
	$message .= admin_url()."admin.php?page=fp_admin_pending_reviews_page\r\n\r\n";
	$message .= __("Thanks for choosing Rich Reviews,", 'rich-reviews'). "\r\n";
	$message .= __("The Nuanced Media Team", 'rich-reviews');

	$mail_subject = __('New Pending Review', 'rich-reviews');

	mail($options['admin-email'], $mail_subject, $message);
}

// Validation for the existence of required fields.

function rr_require_name_field($incomingData) {

	if ($incomingData['reviewer_name'] == '') {
		$incomingData['isValid'] = false;
		$incomingData['errors']['name'] = 'absent required';
	}
	return $incomingData;
}

function rr_require_title_field($incomingData) {

	if ($incomingData['review_title'] == '') {
		$incomingData['isValid'] = false;
		$incomingData['errors']['title'] = 'absent required';
	}
	return $incomingData;
}

function rr_require_email_field($incomingData) {

	if ($incomingData['reviewer_email'] == '') {
		$incomingData['isValid'] = false;
		$incomingData['errors']['email'] = 'absent required';
	}
	return $incomingData;
}

function rr_require_content_field($incomingData) {
	if ($incomingData['review_text'] == '') {
		$incomingData['isValid'] = false;
		$incomingData['errors']['content'] = 'absent required';
	}
	return $incomingData;
}

function rr_require_rating_field($incomingData) {
	if ($incomingData['review_rating'] == 0) {
		$incomingData['isValid'] = false;
		$incomingData['errors']['rating'] = 'absent required';
	}
	return $incomingData;
}

// Field Specific Validation ('rr_misc_validation')

function rr_validate_name_length($incomingData) {
	if (strlen($incomingData['reviewer_name']) > 40) {
		$incomingData['isValid'] = false;
		$incomingData['errors']['name'] = 'length violation';
	}
	return $incomingData;
}

function rr_validate_email($incomingData) {

	if ($incomingData['reviewer_email'] != '') {
		if (strlen($incomingData['reviewer_email']) > 150 ) {
			$incomingData['isValid'] = false;
			$incomingData['errors']['email'] = 'length violation';
		} else {
			$firstAtPos = strpos($incomingData['reviewer_email'],'@');
			$periodPos  = strpos($incomingData['reviewer_email'],'.');
			$lastAtPos  = strpos($incomingData['reviewer_email'],'@');
			if (($firstAtPos === false) || ($firstAtPos != $lastAtPos) || ($periodPos === false)) {
				dump("g please");
					$incomingData['isValid'] = false;
					$incomingData['errors']['email'] = 'invalid input';
			}
		}
	}
	return $incomingData;
}

function rr_validate_title_length($incomingData) {
	if ($incomingData['review_title'] != '' ) {
		if (strlen($incomingData['review_title']) > 40) {
			$incomingData['isValid'] = false;
			$incomingData['errors']['title'] = 'length violation';
		}
	}
	return $incomingData;
}

function rr_validate_content_length($incomingData) {
	if ($incomingData['review_text'] != '' ) {
		if (strlen($incomingData['review_title']) > 300) {
			$incomingData['isValid'] = false;
			$incomingData['errors']['content'] = 'length violation';
		}
	}
	return $incomingData;
}




function fp_sanitize($input) {

	if (is_array($input)) {
		foreach($input as $var=>$val) {
			$output[$var] = fp_sanitize($val);
		}
	}
	else {
		if (get_magic_quotes_gpc()) {
			//$input = stripslashes($input);
		}
		$input  = clean_input($input);
		//$output = mysql_real_escape_string($input);
		$output = $input;
	}
	return $output;
}

function clean_input($input) {
		/*$search = array(
			'@<script[^>]*?>.*?</script>@si',   // strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'         // strip multi-line comments
		);
		$output = preg_replace($search, '', $input);*/
		$handling = $input;

		/*$handling = strip_tags($handling);
		$handling = stripslashes($handling);
		$handling = esc_html($handling);
		$handling = mysql_real_escape_string($handling);*/

		$handling = sanitize_text_field($handling);
		$handling = stripslashes($handling);

		$output = $handling;
		return $output;
	}
