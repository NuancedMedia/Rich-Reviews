<?php



function handle_slider($reviews, $options) {
	global $wpdb;
	global $post;

	// Show the reviews
	if (count($reviews)) {
		$total_count = count($reviews);
		$review_count = 0;
		?> <div class="rich-slider"> <?php
			handle_show_slider($reviews, $options);
		?>
			</div>
		<?php
		if($options['display_full_width']){
			do_action('rr_close_testimonial_group', $options);
		}
	}
}

function handle_show_slider($reviews, $options) {
		global $wpdb;
		global $post;
		$output = '';

		// Set up the SQL query


		// Show the reviews
		if (count($reviews)) {
			$total_count = count($reviews);
			$review_count = 0;
			if($options['display_full_width']) {
				foreach($reviews as $review) {
					display_review($review, $options);
				}
			} else {
				?> <div class="testimonial_group"> <?php
				foreach($reviews as $review) {
					display_review($review, $options);
					$review_count += 1;
					if ($review_count == 3) {

						// end the testimonial_group
						?> <div class="clear"></div></div>

						<!-- clear the floats -->
						 <?php

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
					<?php
				}

			}

		}
	}

function build_review_slide($review, $options) {
	// dump($review);

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
			'rFull'		=> false
		);

		//$rAuthorImage = $review->reviewer_image_id;


		for ($i=1; $i<=$data['rRatingVal']; $i++) {
			$data['rRating'] .= '&#9733;'; // orange star
		}
		for ($i=$data['rRatingVal']+1; $i<=5; $i++) {
			$data['rRating'] .= '&#9734;'; // white star
		}


		$data['rDate'] = $data['rWday'] . ', ' . $data['rMonth'] . ' ' . $data['rDay'] . ', ' . $data['rYear'];

		extract($data);
	?>
		<div class="rr-slide <?php echo $review->review_category; ?>">
			<div class="one-half left">
				<h5><?php echo $rTitle; ?></h5>
				<date><?php echo $rDate; ?></date>
				<br />
				<span><?php echo $rRating; ?></span>
			</div>
			<div class="one-half right">
				<p>
					<?php echo $rText; ?>
				</p>
				<cite class="right"> - <?php echo $rName; ?></cite>
			</div>
			<div class="clear"></div>
		</div>


	<?php
}
