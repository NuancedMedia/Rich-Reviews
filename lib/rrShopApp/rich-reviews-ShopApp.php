<?php

require_once('rich-reviews-ShopAppOptions.php');
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

		add_shortcode('shopper-approved', array(&$this, 'display_handle'));
		add_action('admin_menu', array(&$this, 'add_admin_page'));
		add_action('init', array(&$this, 'init'));
		 add_shortcode('run_pull', array($this, 'process_reviews_pull'));
		date_default_timezone_set('MST');


	}

	function init() {
		$this->options->update_options();
		add_action('update_cache', array(&$this, 'cron_update_cache'));

		if ( ! wp_next_scheduled( 'update_cache' ) ) {
		  wp_schedule_event( time(), 'daily', 'update_cache' );
		}
	}

	function cron_update_cache() {
		$this->process_cron_update();
	}

	function display_handle($atts) {
		$stuff = $this->options->get_option();
		if(isset($stuff['markup']) && $stuff['markup'] != '') {
			$html = $stuff['markup'];
			return $html;
		}
			return;
	}

	function add_admin_page() {
		add_menu_page( 'Shop App Cache', 'Shop App Cache', 'manage_options', 'shop_app_aid_menu', array(&$this, 'display_shop_app_aid_menu'));
	}

	function process_cache_update($data) {

		if($data == null) {
			$data = $this->shopAppOptions;
		}
		// extract($data);
		//make sure the api_url is actually pointing at a file file before trying to retreive content. If not everything will simply remain unchanged.
		$pattern = '/https:\/\/www\.shopperapproved\.com\//';
        $accurate_call = preg_match($pattern, $data['api_url']);
        if(!$accurate_call) {
        	//bad api call
        	return;
        }

        $data = $this->update_site_keys($data);

		//dump(wp_remote_fopen($api_url));


		$this->update_reviews_general_info($data);

        $data = $this->update_aggregate_snippet_markup($data);

		// if(isset($markup) && $markup != null && $markup != '') {
		$data['last_update'] = date("F j, Y, g:i a");

			// $return = array(
			// 	'api_url' 		=> 	$api_url,
			// 	'markup'		=>	$markup,
			// 	'last_update'	=>	$last_update
			// );

		return $data;
		// } else {
		// 	// did not properly return markup
		// 	return;
		// }
	}

	public function update_site_keys($data) {

        if(!isset($data['api_url']) || $data['api_url'] == '') {
          return $data;
        } else {
          $url = $data['api_url'];
        }

        $urlParts = explode('?', $url);

        if(!is_array($urlParts) || !isset($urlParts[1]) || count($urlParts) !=  2) {
          return $data;
        }
        $urlParamString = $urlParts[1];
        parse_str($urlParamString);
        dump($token);
        if(isset($siteid) && isset($token)) {
            $data['site_id'] = $siteid;
            $data['site_token'] = $token;
        }

        return $data;
    }

    public function update_aggregate_snippet_markup($data) {

    	if(!isset($data['site_id']) || $data['site_id'] == '' || !isset($data['site_token']) || $data['site_token'] == '' ) {
            return $data;
        }

        $json_request = "https://www.shopperapproved.com/feeds/schema.php?siteid=" . $data['site_id'] . "&token=" . $data['site_token'];


    	if(wp_remote_fopen($json_request) != false) {
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
        if(!isset($data['site_id']) || $data['site_id'] == '' || !isset($data['site_token']) || $data['site_token'] == '' ) {
            return $data;
        }

        $json_request = "https://www.shopperapproved.com/api/sites/?siteid=" . $data['site_id'] . "&token=" . $data['site_token'];
        if(wp_remote_fopen($json_request) != false) {
          $response = file_get_contents($json_request);
        } else {
          // No file at url
          return $data;
        }
        $response = json_decode($response);

        if(isset($response->average)) {
          $this->options->update_option(array('average_score' => $response->average));
        }

        if(isset($response->review_count)) {
          $this->options->update_option(array('total_review_count' => $response->review_count));
        }

        dump($this->options->get_option());
    }

    public function process_reviews_pull() {

		$data = $this->shopAppOptions;
 		if(!isset($data['site_id']) || $data['site_id'] == '' || !isset($data['site_token']) || $data['site_token'] == '' ) {
            return $data;
        }

        $current_pulled_reviews = $data['reviews_pulled_count'];
        $total_shop_app_reviews = $data['total_review_count'];

        //Need to figure out how to sync effectively, probably using date params or page. Issue is only 100 reviews are pulled at a time.

        $reviews_json = $this->fetch_shopper_approved_reviews($data);
        $reviews_array = json_decode($reviews_json);

        $inserted_ids = $data['imported_review_ids'];
        foreach($reviews_array as $id => $review_object) {
        	if(!in_array($id, $inserted_ids)) {
        		$this->insert_shop_app_review($review_object, $id);
        	}
        }

        dump($reviews_array);

    }

    public function insert_shop_app_review($review, $id) {

    	$options = $this->options->get_option();

    	if(in_array($id, $options['imported_review_ids'])) {
    		return;
    	}

    	dump($review);
    	$date = $this->reformat_date($review->displaydate);

    	if($review->public == true) {
    		$review_status = 1;
    	} else {
    		$review_status = 0;
    	}

    	dump($review->textcomment);
    	$text = str_replace("/'", "'", $review->textcomments);
    	dump($text);

    	$newSubmission = array(
			'date_time'       => $date,
			'reviewer_name'   => $review->name,
		// 	// 'reviewer_image_id' => $newData['reviewer_image_id'],
		// 	'reviewer_email'  => $newData['reviewer_email'],
		// 	'review_title'    => $newData['review_title'],
			'review_rating'   => intval($review->Overall),
		// 	// 'review_image_id' => $newData['review_image_id'],
			'review_text'     => $review->textcomments,
			'review_status'   => $review_status,
		// 	'reviewer_ip'     => $newData['reviewer_ip'],
		// 	'post_id'		  => $newData['post_id'],
			'review_category' => 'shopperApproved',
		);

    	dump($options['imported_review_ids']);
    	array_push($options['imported_review_ids'], $id);
    	$this->options->update_option('imported_review_ids', $options['imported_review_ids']);
    	dump($this->options->get_option());
		rr_insert_new_review($newSubmission, $this->parent->rr_options, $this->parent->sqltable);

    }

    public function reformat_date($date) {
    	$parts = explode(' ', $date);
    	if(is_array($parts) && count($parts) == 3) {
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

    	if(!isset($data['site_id']) || $data['site_id'] == '' || !isset($data['site_token']) || $data['site_token'] == '' ) {
            return $data;
        }

        $json_request = 'https://www.shopperapproved.com/api/reviews/?siteid=' . $data['site_id'] . '&token=' . $data['site_token'] . '';

        //maybe append remaining parameters, based on options or synching strategy

        if(wp_remote_fopen($json_request) != false) {
			$reviews_json = file_get_contents($json_request);
			return $reviews_json;
		} else {
			// No file at url
			return null;
		}


    }

	// function pull_new_SA_reviews() {
	// 	$query =
	// }

	// Nice try but get_headers throws errors if domain doesn't exist

	// function url_exists($url) {
	// 	$headers = get_headers($url);
	// 	if(strpos($headers[0], "200 OK")) {
	// 		return true;
	// 	}
	// 	return false;
	// }

	// helper for file_get_contents() pre-call check
	// function is_url_exist($url){
	//     $ch = curl_init($url);
	//     curl_setopt($ch, CURLOPT_NOBODY, true);
	//     curl_exec($ch);
	//     $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	//     if($code == 200){
	//        $status = true;
	//     }else{
	//       $status = false;
	//     }
	//     curl_close($ch);
	//    return $status;
	// }

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

	function display_shop_app_aid_menu() {
		$this->options->update_options();
		?>
		<div class="rr_shortcode_container">
		<h1>Shopper Approved Cache Menu</h1>
		<br />
		<!-- check if this is already set -->
		<form id="shopAppAdmin"method="post" action="">
			<input type="hidden" name="dinner" value="served" />
			<div class="label-container one-fifth" style="width:30%;float:left;">
				<label for="api_url" style="float:right;font-size:18px;">
					Enter API Url:
				</label>
			</div>
			<div class="input-container two-thirds" style="width:66%;float:right;">
				<input type="text" name="api_url" style="width: 70%;float:left;" placeholder="API URL" style="vertical-align:bottom;" <?php if($this->shopAppOptions['api_url'] != null) { echo 'value="' . $this->shopAppOptions['api_url'] . '"';} ?> />
			</div>
			<div class="clear"></div>
			<br />
			<div class="input-container two-thirds" style="width:66%;float:right;">
			<div style="width: 70%;float:left;">
				<input type="submit" class="button" id="submit-api-url" value="Submit Url" style="float:right;" />
			</div>
		</div>
		</form>
		<div class="clear"></div>
		<br />
		<div class="label-container one-fifth" style="width:30%; float:left;">
			<label for="html-markup" style="float:right;font-size:18px;">
				Shopper Approved Markup:
				<div style="font-size: 11px; font-weight: 400; margin-top: 8px; font-style: italic;">Use the shortcode <code style="font-style: normal; font-size: 11px;">[shopper-approved]</code> to output this markup</div>
			</label>
		</div>
		<div class="input-container two-thirds" style="width:66%;float:right;">
			<code name="Shopper Approved" placeholder="API Key" rows="10" cols="70" style="overflow:scroll;width:70%;float:left;" >
			<?php if($this->shopAppOptions['markup'] != null) { echo htmlspecialchars($this->shopAppOptions['markup']);} ?>
			</code>
		</div>
		<div class="clear"></div>
		<br />
		<div class="label-container one-fifth" style="width:30%;float:left;">
			<label for="last_update" class="one-third" style="float:right;font-size:18px;">
				Last Updated:
			</label>
		</div>
		<div class="input-container two-thirds" style="width:66%;float:right;">
			<input type="text" name="last_update" disabled class="two-thirds" style="width:70%;float:left;" <?php if($this->shopAppOptions['last_update'] != null) { echo 'value="' . $this->shopAppOptions['last_update'] . '"';} ?>/>
		</div>
		<div class="clear"></div>
		<br/>
		<div class="label-container one-fifth" style="width:30%;float:left;">
			<input type="submit" form="shopAppAdmin" class="button" id="force-update" style="float:right;" value="Manual Update"/>
		</div>
		</div>

		<style>
			.postbox-container {
				float:none !important;
			}
		</style>


		<?php
		// dump($this->shopAppOptions);
	}
}

/**
 * dump function for debug
 */
if (!function_exists('dump')) {
    function dump ($var, $label = 'Dump', $echo = TRUE) {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left; width: 100% !important; font-size: 12px !important;">' . $label . ' => ' . $output . '</pre>';
        if ($echo == TRUE) {
            echo $output;}else {return $output;}
    }
}

$shopperApprovedCache = new RRShopApp();

?>
