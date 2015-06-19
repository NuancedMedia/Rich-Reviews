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
			$output .= '<div class="testimonial_group">';
			foreach($reviews as $review) {
				$output .= display_review($review, $options);
				$review_count += 1;
				if ($review_count == 3) {
					// end the testimonial_group
					$output .= '</div>';

					// clear the floats
					$output .= '<div class="clear"></div>';

					// do we have more reviews to show?
					if ($review_count < $total_count) {
						$output .= '<div class="testimonial_group">';
					}

					// reset the counter
					$review_count = 0;
					$total_count = $total_count - 3;
				}
			}
			// do we need to close a testimonial_group?
			if ($review_count != 0) {
				$output .= '</div>';
				$output .= '<div class="clear"></div>';
			}

		}
		$output .= print_credit($options);
		render_custom_styles($options);
		return __($output, 'rich-reviews');
	}

	function display_review($review, $options) {

		$rID        = $review->id;
		$rDateTime  = $review->date_time;
		$date 		= strtotime($rDateTime);
		$rDay		= date("j", $date);
		$rMonth		= date("F", $date);
		$rWday		= date("l", $date);
		$rYear		= date("Y", $date);
		$rDate 		= $rWday . ', ' . $rMonth . ' ' . $rDay . ', ' . $rYear;
		// $rDate 		= $rMonth . ' ' . $rDay . $rSuffix . ', '  . $rYear;
		$rName      = $review->reviewer_name;
		$rEmail     = $review->reviewer_email;
		$rTitle     = $review->review_title;
		$rRatingVal = max(1,intval($review->review_rating));
		$rText      = $review->review_text;
		$rStatus    = $review->review_status;
		$rIP        = $review->reviewer_ip;
		$rPostId    = $review->post_id;
		$rRating = '';
		//$rAuthorImage = $review->reviewer_image_id;


		for ($i=1; $i<=$rRatingVal; $i++) {
			$rRating .= '&#9733;'; // orange star
		}
		for ($i=$rRatingVal+1; $i<=5; $i++) {
			$rRating .= '&#9734;'; // white star
		}

		// $output = '<div class="testimonial">
		// 	<h3 class="rr_title">' . $rTitle . '</h3>
		// 	<div class="clear"></div>';
		// if ($options['show_form_post_title']) {
		// 	$output .= '<div class="rr_review_post_id"><a href="' . get_the_permalink($rPostId) . '">' . get_the_title($rPostId) . '</a></div><div class="clear"></div>';
		// }
		// $output .= '<div class="stars">' . $rRating . '</div>
		// 	<div class="clear"></div>';
		// $output .= '<div class="rr_review_text"><span class="drop_cap">“</span>' . $rText . '”</div>';
		// $output .= '<div class="rr_review_name"> - ' . $rName . '</div>
		// 	<div class="clear"></div>';
		// $output .= '</div>';

		#TODO: Rework output for rich data, image, and up/down vote
		if($options['display_full_width'] != NULL) {
			$output = '<div class="full-testimonial" itemscope itemtype="http://schema.org/Review">';
			$output .= '<div class="review-head">';
			// if($rAuthorImage) {
			// 	dump($rAuthorImage);
			// 	$output .= '<div class="user-image">';
			// 	$output .= wp_get_attachment_image( $rAuthorImage, [70, 70]);
			// 	$output .= '</div>';
			// }
			$output .= '<div class="review-info">';
			if( $rTitle != '') {
				$output .= '<h3 class="rr_title">' . $rTitle . '</h3>';
			} else {
				$output .= '<h3 class="rr_title" style="display:none">' . $rTitle . '</h3>';
			}
			$output .= '<div class="clear"></div>';
		} else {
			$output = '<div class="testimonial" itemscope itemtype="http://schema.org/Review">';
			if( $rTitle != '') {
				$output .= '<h3 class="rr_title" itemprop="name">' . $rTitle . '</h3>';
			} else {
				$output .= '<h3 class="rr_title" style="display:none">'.$rTitle . '</h3>';
			}
			$output .= '<div class="clear"></div>';
		}
		if ($options['show_form_post_title']) {
			$output .= '<span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product"><div class="rr_review_post_id" itemprop="name"><a href="' . get_permalink($rPostId) . '">' . get_the_title($rPostId) . '</a></div><div class="clear"></div></span>';
		} else {
			$output .= '<div class="rr_review_post_id" itemprop="itemreviewed" style="display:none;"><a href="' . get_permalink($rPostId) . '">' . get_the_title($rPostId) . '</a></div><div class="clear"></div>';
		}
		#TODO: Double check to ensure date formatting is correct
		if ($options['show_date']) {
			if($rDateTime != "0000-00-00 00:00:00") {
				$output .= '<span class="rr_date"><meta itemprop="datePublished" content="'.$rDateTime.'"><time datetime="' . $rDate . '">' . $rDate . '</time></span>';
			} else {
				if(current_user_can('edit_posts')) {
				$output .= '<span class="date-err rr_date">' . __('Date improperly formatted, correct in ', 'rich-reviews') . '<a href="/wp-admin/admin.php?page=fp_admin_approved_reviews_page">' . __('Dashboard', 'rich-reviews') . '</a></span>';
				}
			}
		}
		$output .= '<div class="stars">' . $rRating . '</div><div style="display:none;" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><span itemprop="ratingValue">' . $rRatingVal . '</span><span itemprop="bestRating">5</span><span itemprop="worstRating">1</span></div>';

		$output .= '<div class="clear"></div>';
		if($options['display_full_width']) {
			$output .= '</div></div>';
		}
		if($rText != '') {
			$output .= '<div class="rr_review_text"  ><span class="drop_cap">“</span><span itemprop="reviewBody">' . $rText . '</span>”</div>';
		}
		if( $rName !='' ) {
			$output .= '<div class="rr_review_name" itemprop="author" itemscope itemtype="http://schema.org/Person"> - <span itemprop="name">' . $rName . '</span></div>';
		}
		$output .=	'<div class="clear"></div>';
		$output .= '</div>';
		return __($output, 'rich-reviews');


	}

function print_credit($options) {
	$permission = $options['credit_permission'];
	$output = "";
	if ($permission) {
		$output = '<div class="credit-line">' . __('Supported By: ', 'rich-reviews') . '<a href="http://nuancedmedia.com/" rel="nofollow">' . 'Nuanced Media'. '</a>';
		$output .= '</div>' . PHP_EOL;
		$output .= '<div class="clear"></div>' . PHP_EOL;
	}
	return __($output, 'rich-reviews');
}
