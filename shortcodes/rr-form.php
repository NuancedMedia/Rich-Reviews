<?php

function handle_form($atts, $options, $sqltable) {
	global $wpdb;
	global $post;
	extract(shortcode_atts(
		array(
			'category' => 'none',
		)
	,$atts));
	$output = '';
	$rName  = '';
	$rEmail = '';
	$rTitle = '';
	$rText  = '';
	$nameErr = '';
	$emailErr = '';
	$titleErr = '';
	$reviewErr = '';
	$textErr = '';
	$displayForm = true;
	if (isset($_POST['submitted'])) {
		if ($_POST['submitted'] == 'Y') {
			$incomingData = $_POST;
			dump('before: ');
			dump($incomingData);
			apply_filters('rr_process_form_data', $incomingData);
			dump('after: ');
			dump($incomingData);
			$rDateTime = date('Y-m-d H:i:s');
			$incomingData['rDateTime'] = $rDateTime;
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
			if ($options['require_approval']) {$rStatus   = 0;} else {$rStatus   = 1;}
			$rIP       = $_SERVER['REMOTE_ADDR'];
			$rPostID   = $post->ID;
			$rCategory = fp_sanitize($category);


			dump($rAuthorImage);
			dump($rImage);

			$newdata = array(
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
					'review_category' => $rCategory
			);
			$validData = true;
			if($options['form-name-display']) {
				if($options['form-name-require']) {
					if ($rName == '') {
					$nameErr = '<span class="form-err">' . __('You must include your name.', 'rich-reviews') . '</span><br>';
					$validData = false;
					}
				}
			}
			if($options['form-title-display']) {
				if($options['form-title-require']) {
					if ($rTitle == '') {
						$titleErr= '<span class="form-err">' . __('You must include a title for your review.', 'rich-reviews') . '</span><br>';
						$validData = false;
					}
				}
			}
			if($options['form-content-display']) {
				if($options['form-content-require']) {
					if ($rText == '') {
						$textErr = '<span class="form-err">' . __('You must write some text in your review.', 'rich-reviews') . '</span><br>';
						$validData = false;
					}
				}
			}

			if ($rRating == 0) {
				$reviewErr = '<span class="form-err">' . __('Please give a rating between 1 and 5 stars.', 'rich-reviews') . '</span><br>';
				$validData = false;
			}
			if($options['form-email-display']) {
				if($options['form-email-require']) {
					if($rEmail == '') {
						$emailErr = '<span class="form-err">' . __('Please provide email.', 'rich-reviews') . '</span><br>';
					}
				}
				if ($rEmail != '') {
					$firstAtPos = strpos($rEmail,'@');
					$periodPos  = strpos($rEmail,'.');
					$lastAtPos  = strrpos($rEmail,'@');
					if (($firstAtPos === false) || ($firstAtPos != $lastAtPos) || ($periodPos === false)) {
						$emailErr .= '<span class="form-err">' . __('You must provide a valid email address.', 'rich-reviews') . '</span><br>';
						$validData = false;
					}
				}
			}
			if ($validData) {
				if($options['form-name-display']) {
					if ((strlen($rName) > 100)) {
						$output .= __('The name you entered was too long, and has been shortened.', 'rich-reviews') . '<br />';
					}
				}
				if($options['form-title-display']) {
					if ((strlen($rTitle) > 150)) {
						$output .= __('The review title you entered was too long, and has been shortened.', 'rich-reviews') . '<br />';
					}
				}
				if($options['form-email-display']) {
					if ((strlen($rEmail) > 100)) {
						$output .= __('The email you entered was too long, and has been shortened.', 'rich-reviews') . '<br />';
					}
				}
				if( $options['send-email-notifications']) {
					sendEmail($newdata, $options);
				}
				$wpdb->insert($sqltable, $newdata);
				$output .= '<span id="state"></span>';

				//TODO: format for i18n
				$output .= '<div class="successful"><span class="rr_star glyphicon glyphicon-star left" style="font-size: 34px;"></span><span class="rr_star glyphicon glyphicon-star big-star right" style="font-size: 34px;"></span><center><strong>' . $rName . ', your review has been recorded';
				if($options['require_approval']) {
					$output .= ' and submitted for approval';
				}
				$output .= '. Thanks!</strong></center><div class="clear"></div></div>';
				$displayForm = false;
			} else {
				//$output .= '<span id="target"></span>';
			}
		}
	} else {
		$output .= '<span id="state"></span>';
	}
	if ($displayForm) {
		$errors = array(
			'name' 		=> 	$nameErr,
			'email'		=>	$emailErr,
			'title' 	=>	$titleErr,
			'content'	=>	$textErr,
			'rating'	=>	$ratingErr
		);
		?>
		<form action="" method="post" enctype="multipart/form-data" class="rr_review_form" id="fprr_review_form">
			<input type="hidden" name="submitted" value="Y" />
			<input type="hidden" name="rRating" id="rRating" value="0" />
			<table class="form_table">
			<?php do_action('rr_do_form_fields', $options, $newData, $errors); ?>
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

function sanitize_incoming_data($incomingData) {

	$modifiedData = array();
	foreach($incomingData as $field => $val) {
		$incomingData[$field] = $val . 'er';
	}
		dump($incomingData);
	// $incomingData = $modifiedData;
	return $incomingData;
}

function rr_do_rating_field($options, $rData = null, $errors = null) {
	$ratingErr = $errors['rating'];

	@include '/../views/frontend/form/rr-star-input.php';

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

function rr_do_name_field($options, $rData = null, $errors = null) {
	$inputId = 'Name';
	$require = false;
	$rName = '';
	$error = $errors['name'];
	$label = $options['form-name-label'];
	if($options['form-name-require']) {
		$require = true;
	}
	if($rData['reviewer_name']) {
		$rName = $rData['reviewer_name'];
	}

	@include '/../views/frontend/form/rr-text-input.php';
}

function rr_do_email_field($options, $rData = null, $errors = null) {
	$inputId = 'Email';
	$require = false;
	$rEmail = '';
	$error = $errors['email'];
	$label = $options['form-email-label'];
	if($options['form-email-require']) {
		$require = true;
	}
	if($rData['reviewer_email']) {
		$rEmail = $rData['reviewer_email'];
	}

	@include '/../views/frontend/form/rr-text-input.php';
}

function rr_do_title_field($options, $rData = null, $errors = null) {
	$inputId = 'Title';
	$require = false;
	$rTitle = '';
	$error = $errors['title'];
	$label = $options['form-title-label'];
	if($options['form-title-require']) {
		$require = true;
	}
	if($rData['reviewer_title']) {
		$rTitle = $rData['reviewer_title'];
	}

	@include '/../views/frontend/form/rr-text-input.php';
}

function rr_do_content_field($options, $rData = null, $errors = null) {
	dump("in");
	$inputId = 'Text';
	$require = false;
	$rText = '';
	$error = $errors['content'];
	dump($error);
	$label = $options['form-content-label'];
	if($options['form-content-require']) {
		$require = true;
	}
	dump($require);
	if($rData['review_text']) {
		$rText = $rData['review_text'];
	}

	@include '/../views/frontend/form/rr-textarea-input.php';
}

function sendEmail($data, $options) {

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
	if( $reviewer_email != "" ) {
		$message .= $options["form-email-label"].": ".$reviewer_email."\r\n";
	}
	if( $review_title != "" ) {
		$message .= $options["form-title-label"].": ".$review_title."\r\n";
	}
	$message .= __("Review Rating: ", 'rich-reviews'). $review_rating ."\r\n";
	if ($review_text != "" ) {
		$message .= $options["form-content-label"].": ".$review_text."\r\n";
	}
	$message .= __("Review Category: ", 'rich-reviews'). $review_category ."\r\n\r\n";

	$message .= __("Click the link below to review and approve your new review.", 'rich-reviews'). "\r\n";
	$message .= admin_url()."admin.php?page=fp_admin_pending_reviews_page\r\n\r\n";
	$message .= __("Thanks for choosing Rich Reviews,", 'rich-reviews'). "\r\n";
	$message .= __("The Nuanced Media Team", 'rich-reviews');

	$mail_subject = __('New Pending Review', 'rich-reviews');

	mail($options['admin-email'], $mail_subject, $message);
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
