<?php

	function handle_snippet($data, $options) {

		extract($data);

		if ($options['snippet_stars']) {
			$use_stars = TRUE;
			$decimal = $average - floor($average);
			if($decimal >= 0.5) {
				$roundedAverage = floor($average) + 1;
			} else {
				$roundedAverage = floor($average);
			}
			$stars = '';
			$star_count = 0;
			//dump($averageRating, 'AVE:');
			for ($i=1; $i<=5; $i++) {
				if ($i <= $roundedAverage) {
					$stars = $stars . '&#9733;';
				}
				else {
					$stars = $stars . '&#9734;';
				}
			}
		}
		$data = array(
			'use_stars'		=> $use_stars,
			'category' 		=> $category,
			'stars'			=> $stars,
			'average'		=> $average,
			'reviewsCount'	=> $reviewsCount,
			'options'		=> $options
		);

		include '/../views/frontend/snippets.php';

			// ----- Old Star handling that broke Google Snippets because
			// ----- $average rating was being altered.

			// while($averageRating >= 1) {
			// 	$stars = $stars . '&#9733';
			// 	$star_count++;
			// 	$averageRating--;
			// 	//dump($averageRating, 'AVE in WHILE:');
			// 	//dump($star_count, 'STAR COUNT:');
			// }
			// while ($star_count < 5) {
			// 	$stars = $stars . '&#9734';
			// 	$star_count++;
			// 	//dump($star_count, 'STAR COUNT:');
			// }

		// 	$output = '<div class="hreview-aggregate">Overall rating: <span class="stars">' . $stars . '</span> <span class="rating" style="display: none !important;">' . $averageRating . '</span> based on <span class="votes">' . $approvedReviewsCount . '</span> reviews</div>';
		// 	$this->render_custom_styles();
		// } else {
		// 	$output = '<div class="hreview-aggregate">Overall rating: <span class="rating">' . $averageRating . '</span> out of 5 based on <span class="votes">' . $approvedReviewsCount . '</span> reviews</div>';
		// }




	}
