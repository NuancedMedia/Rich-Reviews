<?php

	//
	// This file contains all functions specifically pertinant to the display of Reviews
	// TODO: Modify to use filters

	function handle_show($reviews, $options) {
		global $wpdb;
		global $post;
		$output = '';

		// Set up the SQL query
		// Show the reviews
		if (count($reviews)) {
			$total_count = count($reviews);
			$review_count = 0;
			?> <div class="testimonial_group"> <?php
			foreach($reviews as $review) {
				display_review($review, $options);
				$review_count += 1;
				if ($review_count == 3) {

					// end the testimonial_group
					?> </div>

					<!-- clear the floats -->
					<div class="clear"></div> <?php

					// do we have more reviews to show?
					if ($review_count < $total_count) {
						?> <div class="testimonial_group"> <?php
					}

					// reset the counter
					$review_count = 0;
					$total_count = $total_count - 3;
				}
			}
			// do we need to close a testimonial_group?
			if ($review_count != 0) {
				?>
				</div>
				<div class="clear"></div>
				<?php
			}

		}
		do_action('rr_close_testimonial_group', $options);
	}

	function display_review($review, $options) {

		$date = strtotime($review->date_time);
		$data = array(
			'rID'       => $review->id,
			'rDateTime' => $review->date_time,
			'date' 		=> strtotime($review->date_time),
			'rDay'		=> date("j", $date),
			'rMonth'	=> date("F", $date),
			'rWday'		=> date("l", $date),
			'rYear'		=> date("Y", $date),
			'rDate' 	=> '',
			// 'rDate' 		=> $rMonth . ' ' . $rDay . $rSuffix . ', '  . $rYear,
			'rName'     => $review->reviewer_name,
			'rEmail'    => $review->reviewer_email,
			'rTitle'    => $review->review_title,
			'rRatingVal'=> max(1,intval($review->review_rating)),
			'rText'     => $review->review_text,
			'rStatus'   => $review->review_status,
			'rIP'       => $review->reviewer_ip,
			'rPostId'   => $review->post_id,
			'rRating' 	=> '',
			'rFull'		=> false,
			'rCategory' => $review->review_category,
			'using_subject_fallback' => false,
			'rich_url'  => $options['rich_url_value']

		);

		if(isset($review->reviewer_id) && $review->reviewer_id != '') {
			$data['rReviewerId'] = $review->reviewer_id;
		}

		if(isset($review->reviewer_image) && $review->reviewer_image != '') {
			$data['rAuthorImage'] = $review->reviewer_image;
		}

		$using_subject_fallback = false;
		$title = $data['rCategory'];
		if(!isset($data['rCategory']) || $data['rCategory'] == '' || strtolower($data['rCategory']) == 'none' || $data['rCategory'] == null ) {
			$page_title = get_the_title($data['rPostId']);
			$using_subject_fallback = true;

			if(isset($page_title) && $page_title != '' && $options['rich_itemReviewed_fallback_case'] == 'both_missing')  {
				$title = $page_title;
			} else {
				$title = $options['rich_itemReviewed_fallback'];
			}
		}

		if($options['rich_itemReviewed_fallback_case'] == 'always') {
			$title = $options['rich_itemReviewed_fallback'];
			$using_subject_fallback = true;
		}

		$data['rCategory'] = $title;
		$data['using_subject_fallback'] = $using_subject_fallback;

		if(!isset($data['rName']) || $data['rName'] == '') {
			if($options['rich_author_fallback'] != '') {
				$data['rName'] = $options['rich_author_fallback'];
			} else {
				$data['rName'] = 'Anonymous';
			}
		}

		// if($options['integrate-user-info'] && $options['form-name-use-avatar']) {
		// 	$rAuthorImage = $review->reviewer_image_id;
		// }


		for ($i=1; $i<=$data['rRatingVal']; $i++) {
			$data['rRating'] .= '&#9733;'; // orange star
		}
		for ($i=$data['rRatingVal']+1; $i<=5; $i++) {
			$data['rRating'] .= '&#9734;'; // white star
		}


		$data['rDate'] = $data['rWday'] . ', ' . $data['rMonth'] . ' ' . $data['rDay'] . ', ' . $data['rYear'];

		if($options['display_full_width']) {
			$data['rFull'] = true;
		}

		do_action('rr_do_review_wrapper', $data);

		$data['options'] = $options;

		do_action('rr_do_review_content', $data);
	}

function full_width_wrapper($data) {
	#TODO: Rework output for rich data, image, and up/down vote
	global $richReviews;
	$options = $richReviews->options->get_option();
	$user = wp_get_current_user();
	#?>
	<div class="full-testimonial" itemscope itemtype="http://schema.org/Review">
		<div class="review-head">

		<?php
		// Handling this elsewhere for now
		//if($options['integrate-user-info'] && $options['form-name-use-avatar']) {
				// dump($data['rAuthorImage']);
				// dump($data['rReviewerId']);
				//if(isset($data['rAuthorImage']) && $data['rAuthorImage'] != '') {
					?> <!-- <div class="user-image"> --> <?php
					//These are also handled on the front end however we check here to insure that if options are changed after the fact of review submission, than the changes will be refelcted in the display of the review.
					//This all has to be handled differently

					// if(isset($data['rReviewerId']) && $data['rReviewerId'] != '') {
					// 	if($options['form-reviewer-image-display'] && $options['form-name-use-avatar']) {
					// 		echo build_avatar_display($data['rAuthorImage']);
					// 	}
					// } else if($options['unregistered-allow-avatar-upload']) {
					// 	if($options['form-reviewer-image-display'] && $options['form-name-use-avatar']) {
					// 		echo build_avatar_display($data['rAuthorImage']);
					// 	}
					// }
					?> <!-- </div> --> <?php
				//}

		//} ?>

		<div class="review-info">
		<h3 class="rr_title"><?php echo $data['rTitle']; ?></h3>
		<div class="clear"></div>
	<?php
}

function build_avatar_display($image_url) {
	if(!isset($image_url) || $image_url == '') {
		return '';
	}
	$markup = '<img alt="" src="' . $image_url . '" srcset="' . $image_url . '" class="rr-avatar avatar avatar-96 photo" height="44" width="44" />';
	return $markup;
}

function column_wrapper ($data) {
	?>
	<div class="testimonial" itemscope itemtype="http://schema.org/Review">
		<h3 class="rr_title" itemprop="name"><?php echo $data['rTitle']; ?></h3>
		<div class="clear"></div>
	<?php
}

function do_post_title ($data) {
	// ob_start();
	if($data['rCategory'] == 'shopperApproved') {
		do_hidden_post_title($data);
	} else if($data['using_subject_fallback'] == true) {
		do_hidden_post_title($data);
	} else {
	?>
		<span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product">
			<div class="rr_review_post_id" itemprop="name" >
				<a href="<?php echo get_permalink($data['rPostId']); ?>">
					<?php echo $data['rCategory']; ?>
				</a>
			</div>

	<?php
	}
	// return ob_get_clean();
}

function do_hidden_post_title ($data) {
	?>
	<span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product">
		<div class="rr_review_post_id" itemprop="name" style="display:none;">
			<a href="<?php if($data['rCategory'] != 'shopperApproved') { echo get_permalink($data['rPostId']); }?>">
				<?php echo $data['rCategory']; ?>
			</a>
		</div>

	<?php
}

function do_url_schema($data) {
	?>
			<a href="http://<?php echo $data['rich_url']; ?>" itemprop="url"></a>
			<div class="clear"></div>
		</span>
	<?php
}

function omit_url_schema($data) {
	?>
		<div class="clear"></div>
	</span>
	<?php
}

function do_the_date ($data) {
	if($data['rDateTime'] != "0000-00-00 00:00:00") {
		// ob_start();
		?>
		<span class="rr_date"><meta itemprop="datePublished" content="<?php echo $data['rDateTime']; ?>">
			<time datetime="<?php echo $data['rDate']; ?>">
				<?php echo $data['rDate']; ?>
			</time>
		</span>
	<?php } else {
		if(current_user_can('edit_posts')) { ?>
		<span class="date-err rr_date">
			<?php echo __('Date improperly formatted, correct in ', 'rich-reviews'); ?>
			<a href="/wp-admin/admin.php?page=fp_admin_approved_reviews_page">
				<?php echo __('Dashboard', 'rich-reviews'); ?>
			</a>
		</span>

	<?php	}
	}
	// return ob_get_clean();
}

function do_the_date_hidden ($data) {
		if($data['rDateTime'] != "0000-00-00 00:00:00") {
		?>
		<span class="rr_date" style="display:none;"><meta itemprop="datePublished" content="<?php echo $data['rDateTime']; ?>">
			<time datetime="<?php echo $data['rDate']; ?>">
				<?php echo $data['rDate']; ?>
			</time>
		</span>
	<?php
	}
}

function do_review_body ($data) {
	?>
		<div class="stars">
			<?php echo $data['rRating']; ?>
		</div>
		<div style="display:none;" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
			<span itemprop="ratingValue">
				<?php echo $data['rRatingVal']; ?>

			</span>
			<span itemprop="bestRating">
				5
			</span>
			<span itemprop="worstRating">
				1
			</span>
		</div>


		<?php if($data['rFull']) {
			?>
				</div> <!-- close .review-info -->
			</div> <!-- close .review-head -->

		<?php } ?>


		<div class="clear"></div>

		<div class="rr_review_text"  ><span class="drop_cap">“</span><span itemprop="reviewBody"><?php echo $data['rText']; ?></span>”</div>
			<div class="rr_review_name" itemprop="author" itemscope itemtype="http://schema.org/Person">
			<span itemprop="name">
			<?php

				$avatar = '';
				if($data['rCategory'] == 'shopperApproved') {
					//probably set an optino for this.
					$avatar =  '  ' . build_shopper_approved_avatar();
				} else if($data['options']['integrate-user-info'] && $data['options']['form-name-use-avatar']) {
					// dump($data['rAuthorImage']);
					// dump($data['rReviewerId']);
					if(isset($data['rAuthorImage']) && $data['rAuthorImage'] != '') {
						//These are also handled on the front end however we check here to insure that if data['options'] are changed after the fact of review submission, than the changes will be refelcted in the display of the review.
						//This all has to be handled differently
						if(isset($data['rReviewerId']) && $data['rReviewerId'] != '') {
							if($data['options']['form-name-use-avatar']) {
								$avatar = '  ' . build_avatar_display($data['rAuthorImage']);
							}
						} else if($data['options']['unregistered-allow-avatar-upload']) {
							if($data['options']['form-name-use-avatar']) {
								$avatar = '  ' . build_avatar_display($data['rAuthorImage']);
							}
						}
					} else if($data['options']['form-name-use-blank-avatar']){
						$avatar = '  ' . get_avatar(0);
					}
				}
				if($avatar != '') {
					echo $data['rName'] . $avatar;
				} else {
					echo '- ' . $data['rName'];
				}
			?>
			</span></div>
			<div class="clear"></div>
		</div>
	<?php
}

function build_shopper_approved_avatar() {

	$markup = '<img alt="" src="' . plugins_url("/RichReviewsGit/images/SA-logo.jpg") . '" srcset="' . plugins_url("/RichReviewsGit/images/SA-logo.jpg") . '" class="rr-avatar sa-avatar" width="44" height="44" style="margin-bottom:8px;"/>';

	return $markup;
}

function print_credit() {
	?>
		<div class="credit-line">
			<?php echo __('Supported By: ', 'rich-reviews'); ?>
			<a href="http://nuancedmedia.com/" rel="nofollow">
				<?php echo 'Nuanced Media'; ?>
			</a>
		</div>
		<div class="clear"></div>
	<?php
}
