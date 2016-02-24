<?php

function handle_form($atts, $options, $sqltable, $path) {
	global $wpdb;
	global $post;
	extract(shortcode_atts(
		array(
			'category' => 'none',
			'unique_key' => 'default'
		)
	,$atts));

	$options['unique_key'] = $unique_key;

	//initialize all data vars
	$rName  = '';
	$rEmail = '';
	$rTitle = '';
	$rText  = '';
	$rAuthorImage = '';
	$rAuthorId = '';
	$output = '';
	$user = wp_get_current_user();
	$displayForm = true;
	$posted = false;
	$errors = array(
		'name'	=>	'',
		'email'	=>	'',
		'title'	=>	'',
		'rating'	=>	'',
		'content'=>	'',
		'reviewer-image' => ''
	);


	$newData = array(
		'reviewer_name'   => $rName,
		'reviewer_image' => $rAuthorImage,
		'reviewer_email'  => $rEmail,
		'review_title'    => $rTitle,
		// 'review_rating'   => intval($rRating),
		// 'review_image_id' => $rImage,
		'review_text'     => $rText,
		'errors'		  => $errors
	);

	if (isset($_POST['submitted'])) {
		if ($_POST['submitted'] == $unique_key) {



			$posted = true;

			$incomingData = $_POST;
			$incomingData = apply_filters('rr_process_form_data', $incomingData);

			if ($options['form-name-display']) {
				if (!isset($_POST['rName'])) {
					if ($options['integrate-user-info'] && $options['form-name-use-usernames']) {
						$rName = $user->data->display_name;
					}
				} else {
					$rName = fp_sanitize($_POST['rName']);
				}
			}
			if ($options['form-name-use-avatar']) {
				if ($user->ID) {
					$rAuthorImage = get_avatar_url($user->ID);
				} else if ($options['unregistered-allow-avatar-upload']) {
					if (isset($_FILES) && count($_FILES) == 1 && isset($_FILES['rrInsertReviewerImageFile'])) {
						if (isset($_POST['rrInsertReviewerImageDisplay']) && $_POST['rrInsertReviewerImageDisplay'] != '') {
							$imgUrl = $_POST['rrInsertReviewerImageDisplay'];
							$fileEnding = strrchr($imgUrl, '.');
							if ($fileEnding) {
								$fileEnding = substr($fileEnding, 1);
								$allowed_file_types = array ('jpg', 'png', 'gif'); //probably make an option for this eventually.
								if (!in_array($fileEnding, $allowed_file_types)) {
									$rAuthorImage = 'Invalid Type';
								} else {
									$imageId = media_handle_upload('rrInsertReviewerImageFile',0);
									if (is_int($imageId)) {
										$rAuthorImage = wp_get_attachment_url($imageId);
									} else {
										$rAuthorImage = 'Invalid Type';
									}

								}
							}
						}
					}
				}
			}

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

			// }
			if ($options['form-content-display']) {
				$rText     = fp_sanitize($_POST['rText']);
			}

			if ($options['integrate-user-info']) {
				if ($user->ID) {
					$rAuthorId = $user->ID;
				}
			}

			$rDateTime = date('Y-m-d H:i:s');
			$incomingData['rDateTime'] = $rDateTime;
			if ($options['require_approval']) {$rStatus   = 0;} else {$rStatus   = 1;}
			$rIP       = $_SERVER['REMOTE_ADDR'];
			$rPostID   = $post->ID;
			$rCategory = fp_sanitize($category);

			$newData = array (

					'date_time'       => $rDateTime,
					'reviewer_name'   => $rName,
					'reviewer_image' => $rAuthorImage,
					'reviewer_email'  => $rEmail,
					'review_title'    => $rTitle,
					'review_rating'   => intval($rRating),
					// 'review_image_id' => $rImage,
					'review_text'     => $rText,
					'review_status'   => $rStatus,
					'reviewer_id'	  => $rAuthorId,
					'reviewer_ip'     => $rIP,
					'post_id'		  => $rPostID,
					'review_category' => $rCategory,
					'isValid'		  => true,
					'errors'		  => $errors

			);


			$newData = apply_filters('rr_check_required', $newData);
			if ($newData['isValid']) {
				$newData = apply_filters('rr_misc_validation', $newData);

			}
			if ($newData['isValid']) {

				$displayForm = false;

				$newSubmission = array(
					'date_time'       => $newData['date_time'],
					'reviewer_name'   => $newData['reviewer_name'],
					'reviewer_image' => $newData['reviewer_image'],
					'reviewer_email'  => $newData['reviewer_email'],
					'reviewer_id'	  => $newData['reviewer_id'],
					'review_title'    => $newData['review_title'],
					'review_rating'   => $newData['review_rating'],
				// 	// 'review_image_id' => $newData['review_image_id'],
					'review_text'     => $newData['review_text'],
					'review_status'   => $newData['review_status'],
					'reviewer_ip'     => $newData['reviewer_ip'],
					'post_id'		  => $newData['post_id'],
					'review_category' => $newData['review_category'],
				);


				do_action('rr_on_valid_data', $newSubmission, $options, $sqltable);
			}
		}
	} else {
		?> <span id="state"></span> <?php
	}
	if ($displayForm) {
		if ($options['require-login'] && !$user->ID) {
			?> <div class="rr_review_form">	<?php
				do_action('rr_display_form_gate', $options);
			?>	</div>  <?php
		} else {

			$errors = $newData['errors'];
			$errors = generate_error_text($errors, $options);

		?>
		<form action="" method="post" enctype="multipart/form-data" class="rr_review_form" id="<? echo $unique_key; ?>">
			<input type="hidden" name="submitted" value="<?php echo $unique_key; ?>" />
			<input type="hidden" name="rRating" id="rRating" value="0" />
			<table class="form_table">
			<?php do_action('rr_do_form_fields', $options, $path, $newData, $errors); ?>

				<tr class="rr_form_row">
					<td></td>
					<td class="rr_form_input"><input id="submitReview" form="<?php echo $unique_key; ?>" type="submit" value="<?php echo $options['form-submit-text']; ?>"/></td>
				</tr>
			</table>
		</form>
		<?php

		}
	}
	do_action('rr_set_local_scripts');
}
function rr_do_form_gate($options) {

	if (isset($options['login-url']) && $options['login-url'] != '' ) {
		$loginUrl = $options['login-url'];
	} else {
		$loginUrl = wp_login_url();
	}
	?>
	<div class="upload-gate">
		<p class="rr-headline">You must be logged in to Submit a Review</p>
		<a href="<?php echo $loginUrl; ?>" class="button button-primary">
			Login/Create Account
		</a>
	</div>
	<style>
		.upload-gate {
			padding-top:0;
			border: solid 3px;
			border-radius: 8px;
			text-align: center;
			margin: 13px 5px;
			padding: 21px;
		}
		.rr-headline {
			font-size: 21px;
			margin: 21px 13px;
		}
		.button {
			padding: 0.4em 1.2em;
			border: solid 3px;
			border-radius: 3px;
		}
	</style>
	<?php
}
function generate_error_text($errors, $options) {


	$processed = array();
	foreach($errors as $key => $val) {
		$option_key = 'form-' . $key . '-label';
		$label = $options[$option_key];
		if ($val == 'absent required') {
			$processed[$key] = 'The ' . $label . ' field is required.';
		} else if ($val == 'invalid input') {
			$processed[$key] = 'Please enter a valid ' . $label;
		} else if ($val == 'length violation') {
			$processed[$key] = 'The ' . $label . ' that you entered is too long.';
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
	$label = $options['form-rating-label'];

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
	$user = wp_get_current_user();
	$disable = false;
	if ($options['form-name-require']) {
		$require = true;
	}
	if ($rData['reviewer_name']) {
		$rFieldValue = $rData['reviewer_name'];
	} else {
		if ($options['integrate-user-info'] && $options['form-name-use-usernames']) {
			if ($user->ID) {
				$rFieldValue = $user->data->display_name;
				$disable = true;
			}
		}
	}

	@include $path . 'views/frontend/form/rr-text-input.php';
}

function rr_do_reviewer_img_field($options, $path, $rData = null, $errors = null) {
	$user = wp_get_current_user();
	if ($user->ID) {
		//do nothing, because we will be using the user avatar.
	} else {
		$require = false;
		$rFieldValue = '';
		$label = $options['form-reviewer-image-label'];
		$error = $errors['reviewer-image'];
		if ($options['form-reviewer-image-require']) {
			$require = true;
		}
		if (isset($rData['reviewer_image']) && $rData['reviewer_image'] != '' ) {
			$rFieldValue = $rData['reviewer_image'];
		}
		//feed needed info.
		@include $path . 'views/frontend/form/rr-img-input.php';
	}
}


function rr_do_email_field($options, $path, $rData = null, $errors = null) {
	$inputId = 'Email';
	$require = false;
	$rFieldValue = '';
	$error = $errors['email'];
	$label = $options['form-email-label'];
	if ($options['form-email-require']) {
		$require = true;
	}
	if ($rData['reviewer_email']) {
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
	if ($options['form-title-require']) {
		$require = true;
	}
	if ($rData['review_title']) {
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
	if ($options['form-content-require']) {
		$require = true;
	}
	if ($rData['review_text']) {

		$rFieldValue = $rData['review_text'];
	}
	@include $path . 'views/frontend/form/rr-textarea-input.php';
}

function rr_insert_new_review($data, $options, $sqltable) {

	global $wpdb;
	$wpdb->insert($sqltable, $data);
}

function rr_output_response_message($data, $options) {

	?>
	<div class="successful">
		<span class="rr_star glyphicon glyphicon-star left" style="font-size: 34px;"></span>
		<span class="rr_star glyphicon glyphicon-star big-star right" style="font-size: 34px;"></span>
		<center>
			<strong>
				<?php

					if ($options['form-name-display'] && $options['form-name-require']) {
						echo $data['reviewer_name'] . ', your review has been recorded';
					} else {
						echo 'Your review has been recorded';
					}
					if ($options['require_approval']) {
						echo ' and submitted for approval';
					}
					echo '. Thanks!';
				?>
			</strong>
		</center>
		<div class="clear"></div>
	</div>
	<?php
}

function rr_output_scroll_script() {

	?>
		<script>
			jQuery(function(){
				if (jQuery(".successful").is(":visible")) {

					offsetOne = jQuery(".successful").offset();
					jQuery("html, body").animate({
						scrollTop: (offsetOne.top - 400)
					});
				} else {
					errorPresent = false;
					jQuery(".form-err").each(function () {
						if (this.innerHTML != ''){
							errorPresent = true;
						}
					});
					if (errorPresent) {

						offsetTwo = jQuery(".form-err.shown").offset();
						jQuery("html, body").animate({
							scrollTop: (offsetTwo.top + 200)
						});
					}
				}
			});
		</script>
	<?php

}

function rr_send_admin_email($data, $options) {

	extract($data);
	$message = "";
	$message .= "RichReviews User,\r\n";
	$message .= "\r\n";
	$message .= __("You have received a new review which is now pending your approval. The information from the review is listed below.", 'rich-reviews') . "\r\n";
	$message .= "\r\n";
	$message .= __("Review Date: ", 'rich-reviews') .$date_time."\r\n";
	if ( $reviewer_name != "" ) {
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
	$message .= admin_url() . "/admin.php?page=fp_admin_pending_reviews_page\r\n\r\n";
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

function rr_require_reviewer_image_field($incomingData) {
	$user = wp_get_current_user();
	if ($user->ID){
		return $incomingData;
	} else {
		if (!isset($incomingData['reviewer_image']) || $incomingData['reviewer_image'] == '') {
			$incomingData['isValid'] = false;
			$incomingData['errors']['reviewer-image'] = 'absent required';
		}
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
					$incomingData['isValid'] = false;
					$incomingData['errors']['email'] = 'invalid input';
			}
		}
	}
	return $incomingData;
}

function rr_validate_reviewer_image_input($incomingData) {
	$user = wp_get_current_user();
	if ($user->ID){
		return $incomingData;
	} else {
		if (isset($incomingData['reviewer_image']) && $incomingData['reviewer_image'] != '') {
			$fileEnding = strrchr($incomingData['reviewer_image'], '.');
			if ($fileEnding) {
				$fileEnding = substr($fileEnding, 1);
				$allowed_file_types = array ('jpg', 'png', 'gif'); //probably make an option for this eventually.
				if (!in_array($fileEnding, $allowed_file_types)) {
					$incomingData['isValid'] = false;
					$incomingData['errors']['reviewer-image'] = 'invalid input';
				}
			} else {
				$incomingData['isValid'] = false;
				$incomingData['errors']['reviewer-image'] = 'invalid input';
			}
		} else {
			$incomingData['isValid'] = false;
			//we shouldn't have gotten this far without
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
