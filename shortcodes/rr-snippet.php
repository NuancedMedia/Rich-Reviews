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

	}
