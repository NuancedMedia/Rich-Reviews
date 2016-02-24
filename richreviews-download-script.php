<?php

		function exportCsv($fields) {
			if(isset($fields['review_text'])) {
				$fields['review_text'] = stripslashes($fields['review_text']);
			}
			if(isset($fields['date_time'])) {
				$fields['date_time'] = date('m/d/y', strtotime($fields['date_time']));
			}
		    $wee = implode('|', $fields);
		    $wee .= "\r\n";
		    return $wee;
		}

		$path = realpath(null);
        $parts = explode('wp-content', $path);
        if (count($parts) == 2) {
            $load_path = $parts[0] . 'wp-load.php';
        } else {
            die();
        }
        require_once($load_path);

        if(!isset($_GET['download']) || $_GET['download'] != 'csv') {
        	die();
        }

        $cur_user = wp_get_current_user();

        if(!$cur_user->ID || !current_user_can('administrator')) {
        	header($_SERVER["SERVER_PROTOCOL"] . " 403.2 Read Access Denied");
        	die();
        }


		global $wpdb, $richReviews;



		$sql = "SELECT
				    reviewer_name, date_time, review_rating, review_text, review_category
				FROM
				    $richReviews->sqltable
				WHERE
				    review_status = 1
				AND
					review_category != 'shopperApproved'";

		$results = $wpdb->get_results($sql);

		$results = (array) $results;

		$file = '';

		if ($wpdb->num_rows) {

		    $file .= exportCsv(array_keys((array) $results[0]));

		    foreach ( $results as $row ) {
		    	$row = (array) $row;
		        $file .= exportCsv($row);
		    }
		}

		$date = date('m/d/Y');

		if (function_exists('mb_strlen')) {
		    $size = mb_strlen($file, '8bit');
		} else {
		    $size = strlen($file);
		}

		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        // header("Content-Type: application/force-download");
        // header("Content-Type: application/octet-stream");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Disposition: attachment; filename="' . $date . '-rr-export.csv"');
        header("Content-Transfer-Encoding: binary ");
        header('Content-Length: '. $size);

        echo $file;

        die();

