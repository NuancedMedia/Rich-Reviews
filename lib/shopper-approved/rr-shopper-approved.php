<?php

require_once('rr-shopper-approved-options.php');
wp_cron();


class RRShopApp {

	var $admin;
	var $option_name;
	var $options;

	function __construct($parent = null) {

		$this->parent = $parent;
		$this->option_name = 'shopAppOptions';
		$this->options = new RRShopAppOptions($this);
		$this->shopAppOptions = $this->options->get_option();

		add_action('admin_menu', array(&$this, 'add_admin_page'));
		add_action('init', array(&$this, 'init'));
		// add_shortcode('run_pull', array($this, 'process_reviews_pull')); //this is currently not working as admin action, but will removed when resolved
		add_shortcode('clear_shop', array($this, 'dump_shop_app_reviews')); //Remove this, or build it into an admin action.
		date_default_timezone_set('MST');
	}

	function init() {
		$this->options->update_options();
		add_action('update_cache', array(&$this, 'cron_update_cache'));

		if (! wp_next_scheduled('update_cache')) {
			wp_schedule_event(time(), 'daily', 'update_cache');
		}
	}

	function cron_update_cache() {
		$this->process_cron_update();
	}

	function display_handle($atts = null) {
		$stuff = $this->options->get_option();
		if (isset($stuff['markup']) && $stuff['markup'] != '') {
			$html = $stuff['markup'];
			return $html;
		}
		return;
	}

	function add_admin_page() {
		add_menu_page('Shop App Cache', 'Shop App Cache', 'manage_options', 'shop_app_aid_menu', array(&$this, 'display_shop_app_aid_menu'));
	}

	function process_cache_update($data = null) {

		if ($data == null) {
			$data = $this->shopAppOptions;
		}
		//make sure the api_url is actually pointing at a file file before trying to retreive content. If not everything will simply remain unchanged.
		$pattern = '/https:\/\/www\.shopperapproved\.com\//';
		$accurate_call = preg_match($pattern, $data['api_url']);
		if (! $accurate_call) {
			//bad api call
			return;
		}

		$data = $this->update_site_keys($data);
		$this->update_reviews_general_info($data);
		$data = $this->update_aggregate_snippet_markup($data);
		$data['last_update'] = date("F j, Y, g:i a");

		return $data;
	}

	public function update_site_keys($data) {

		if (! isset($data['api_url']) || $data['api_url'] == '') {
			return $data;
		} else {
			$url = $data['api_url'];
		}

		$urlParts = explode('?', $url);

		if (! is_array($urlParts) || ! isset($urlParts[1]) || count($urlParts) !=  2) {
			return $data;
		}
		$urlParamString = $urlParts[1];
		parse_str($urlParamString);
		if (isset($siteid) && isset($token)) {
			$data['site_id'] = $siteid;
			$data['site_token'] = $token;
		}

		return $data;
	}

	public function update_aggregate_snippet_markup($data) {

		if (! isset($data['site_id']) || $data['site_id'] == '' || ! isset($data['site_token']) || $data['site_token'] == '' ) {
			return $data;
		}

		$json_request = "https://www.shopperapproved.com/feeds/schema.php?siteid=" . $data['site_id'] . "&token=" . $data['site_token'];

		if (wp_remote_fopen($json_request) != false) {
			$markup = file_get_contents($json_request);
			$data['markup'] = $markup;
		} else {
			// No file at url
			return $data;
		}

		return $data;
	}

	public function update_reviews_info() {
		$data = $this->options->get_option();
		$this->update_reviews_general_info($data);
	}

	public function update_reviews_general_info($data) {
		if (! isset($data['site_id']) || $data['site_id'] == '' || ! isset($data['site_token']) || $data['site_token'] == '') {
			return $data;
		}

		$json_request = "https://www.shopperapproved.com/api/sites/?siteid=" . $data['site_id'] . "&token=" . $data['site_token'];
		if (wp_remote_fopen($json_request) != false) {
			$response = file_get_contents($json_request);
		} else {
			// No file at url
			return $data;
		}
		$response = json_decode($response);

		if (isset($response->average)) {
			$this->options->update_option(array('average_score' => $response->average));
		}

		if (isset($response->review_count)) {
			$this->options->update_option(array('total_review_count' => $response->review_count));
		}

		$updated_options = $this->options->get_option();

	}

	public function process_reviews_pull() {

		$data = $this->shopAppOptions;
		if (! isset($data['site_id']) || $data['site_id'] == '' || ! isset($data['site_token']) || $data['site_token'] == '') {
			return $data;
		}

		$current_pulled_reviews = $data['reviews_pulled_count'];
		$total_shop_app_reviews = $data['total_review_count'];

		// Need to figure out how to sync effectively, probably using date params or page. Issue is only 100 reviews are pulled at a time.

		$reviews_json = $this->fetch_shopper_approved_reviews($data);
		$reviews_array = json_decode($reviews_json);

		$inserted_ids = $data['imported_review_ids'];
		foreach ($reviews_array as $id => $review_object) {
			if (! in_array($id, $inserted_ids)) {
				$this->insert_shop_app_review($review_object, $id);
			}
		}

		$this->options->update_option('reviews_last_pulled', date("F j, Y, g:i a"));
		$stored_review_ids = $this->options->get_option('imported_review_ids');

		$total_pulled = count($stored_review_ids);
		$this->options->update_option('reviews_pulled_count', $total_pulled);
	}

	public function insert_shop_app_review($review, $id) {

		$options = $this->options->get_option();
		if (in_array($id, $options['imported_review_ids'])) {
			return;
		}

		$date = $this->reformat_date($review->displaydate);

		if ($review->public == true) {
			$review_status = 1;
		} else {
			$review_status = 0;
		}

		$text = stripcslashes($review->textcomments);

		$newSubmission = array(
			'date_time'       => $date,
			'reviewer_name'   => $review->name,
			'review_rating'   => intval($review->Overall),
			'review_text'     => $text,
			'review_status'   => $review_status,
			'review_category' => 'shopperApproved',
		);

		array_push($options['imported_review_ids'], $id);
		$this->options->update_option('imported_review_ids', $options['imported_review_ids']);

		$tempRR = new RichReviews();
		rr_insert_new_review($newSubmission, $tempRR->rr_options, $tempRR->sqltable);

	}

	public function dump_shop_app_reviews() {
		global $wpdb;
		$sqltable = $this->parent->sqltable;
		$category = 'shopperApproved';

		$wpdb->query("DELETE FROM $sqltable WHERE review_category=\"$category\"");
		$this->options->update_option('imported_review_ids', array());
	}

	public function reformat_date($date) {
		$parts = explode(' ', $date);
		if (is_array($parts) && count($parts) == 3) {
			$day = $parts[0];
			$year =  $parts[2];

			switch($parts[1]) {
				case 'Jan':
					$month = '01';
					break;
				case 'Feb':
					$month = '02';
					break;
				case 'Mar':
					$month = '03';
					break;
				case 'Apr':
					$month = '04';
					break;
				case 'May':
					$month = '05';
					break;
				case 'Jun':
					$month = '06';
					break;
				case 'Jul':
					$month = '07';
					break;
				case 'Aug':
					$month = '08';
					break;
				case 'Sep':
					$month = '09';
					break;
				case 'Oct':
					$month = '10';
					break;
				case 'Nov':
					$month = '11';
					break;
				case 'Dec':
					$month = '12';
					break;
			}
			$dateString = $month . '/' . $day . '/' . $year;
			$formattedUnix = strtotime($dateString);
			$formattedDate = date('Y-m-d H:i:s', $formattedUnix);
			return $formattedDate;
		} else {
			return null;
		}
	}

	public function fetch_shopper_approved_reviews($data = null) {

		if (! isset($data['site_id']) || $data['site_id'] == '' || ! isset($data['site_token']) || $data['site_token'] == '' ) {
			return $data;
		}

		$json_request = 'https://www.shopperapproved.com/api/reviews/?siteid=' . $data['site_id'] . '&token=' . $data['site_token'] . '';

		//maybe append remaining parameters, based on options or synching strategy

		if (wp_remote_fopen($json_request) != false) {
			$reviews_json = file_get_contents($json_request);
			return $reviews_json;
		} else {
			// No file at url
			return null;
		}


	}

	function process_cron_update() {
		$updated_data = $this->process_cache_update();
		$data = array();
		foreach ($updated_data as $key=>$value) {
			if ($key != 'dinner' && $key != NULL) {
				$data[$key] = $value;
			}
		}
		$this->options->update_option($data);
		$this->shopAppOptions = $this->options->get_option();
	}
}

$shopperApprovedCache = new RRShopApp();
