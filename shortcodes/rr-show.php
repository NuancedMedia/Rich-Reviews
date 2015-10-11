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

	function process_content_and_wrap_links($content = null) {

		$linkWords = array(
			"I had lost all my uni work" => "http://computerrepairnorwich.co.uk/data-recovery/",
			"riddled with viruses" => "http://computerrepairnorwich.co.uk/virus-removal/",
			"cause of the problem, and fixing it superbly" => "http://computerrepairnorwich.co.uk/repairs/",
			"Fantastic customer service" => "http://computerrepairnorwich.co.uk/contact/",
			"repair of my tablet with a new glass" => "http://computerrepairnorwich.co.uk/repairs/",
			"seriously over heating" => "http://computerrepairnorwich.co.uk/health-check/",
			"The service was very efficient" => "http://computerrepairnorwich.co.uk/contact/",
			"Recommended!" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"maximised it's performance" => "http://computerrepairnorwich.co.uk/health-check/",
			"prolonging the life of your computer." => "http://computerrepairnorwich.co.uk/health-check/",
			"identification and resolution of the problem" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"blue screen of death error" => "http://computerrepairnorwich.co.uk/repairs/",
			"all data files were retrieved" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"very reasonable price." => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"keep it virus free" => "http://computerrepairnorwich.co.uk/virus-removal/",
			"Good value for money and excellent service." => "http://computerrepairnorwich.co.uk/health-check/",
			"Very recommended indeed!" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"computer fixed" => "http://computerrepairnorwich.co.uk/repairs/",
			"computer repaired" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"exceptional value for money" => "http://computerrepairnorwich.co.uk/health-check/",
			"Good advise for the future" => "http://computerrepairnorwich.co.uk/contact/",
			"absolutely outstanding service" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"viruses that had infected my system" => "http://computerrepairnorwich.co.uk/virus-removal/",
			"CRN have fixed numerous problems on my laptop" => "http://computerrepairnorwich.co.uk/repairs/",
			"recommended, top notch!" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"honest professionalism and speedy solutions" => "http://computerrepairnorwich.co.uk/contact/",
			"read the excellent comments" => "https://plus.google.com/u/1/b/115897397898038941930/+ComputerrepairnorwichCoUkcrn/posts",
			"machine tuned up" => "http://computerrepairnorwich.co.uk/health-check/",

		);
		if($content == null) {
			return;
		} else {
			$newContent = $content;
			foreach($linkWords as $key => $url) {
				dump($key);
				dump($content);
				$regexPattern = '/'.$key.'/';
				if(preg_match($regexPattern, $content)) {
					dump('found match');
					$frontWrap = '<a href="' . $url . '">';
					$backWrap = '</a>';
					$replacement = $frontWrap . $key . $backWrap;
					$newContent = preg_replace($regexPattern, $replacement, $content);
				}
			}
			return $newContent;
		}
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
			'rText'     => process_content_and_wrap_links($review->review_text),
			'rStatus'   => $review->review_status,
			'rIP'       => $review->reviewer_ip,
			'rPostId'   => $review->post_id,
			'rRating' 	=> '',
			'rFull'		=> false,
			'rCategory' => $review->review_category,
			'using_subject_fallback' => false,
			'rich_url'  => $options['rich_url_value']

		);
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


		//$rAuthorImage = $review->reviewer_image_id;


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

		do_action('rr_do_review_content', $data);
	}

function full_width_wrapper($data) {
	#TODO: Rework output for rich data, image, and up/down vote
	#?>
	<div class="full-testimonial" itemscope itemtype="http://schema.org/Review">
		<div class="review-head">
		<?php //if($data['rAuthorImage']) {
			?>
				<!-- <div class="user-image"> -->
					<?php //wp_get_attachment_image( $data['rAuthorImage'], [70, 70]); ?>
				<!-- </div> -->
			<?php //} ?>
		<div class="review-info">
		<h3 class="rr_title"><?php echo $data['rTitle']; ?></h3>
		<div class="clear"></div>
	<?php
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
	if($data['using_subject_fallback'] == true) {
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
			<a href="<?php echo get_permalink($data['rPostId']); ?>">
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
			<div class="rr_review_name" itemprop="author" itemscope itemtype="http://schema.org/Person"> - <span itemprop="name">
			<?php
				echo $data['rName'];
			?>
			</span></div>
			<div class="clear"></div>
		</div>
	<?php
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
