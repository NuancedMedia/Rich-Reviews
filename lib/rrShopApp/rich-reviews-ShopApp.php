<?php

require_once('rich-reviews-ShopAppOptions.php');
require_once('rich-reviews-ShopAppShortcode.php');

// require_once('views/edit_single_index.php');

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

		add_action('init', array(&$this, 'init'));
        add_action('admin_menu', array(&$this, 'add_edit_product_index'));
        add_action('admin_menu', array(&$this, 'maybe_set_alert_cycle'));
		add_shortcode('clear_shop', array($this, 'dump_shop_app_reviews')); //Remove this, or build it into an admin action.
        add_shortcode('RR_SHOPPER_APPROVED', array(&$this, 'shortcode_shopper_approved_control'));
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

    function maybe_set_alert_cycle() {
        if(!isset($this->parent->rr_options['add-shopper-approved']) || $this->parent->rr_options['add-shopper-approved'] == false) {
            return;
        }
        if(isset($this->shopAppOptions['api_url']) && isset($this->shopAppOptions['site_token']) && $this->shopAppOptions['api_url'] != '' && $this->shopAppOptions['site_token'] != '' ) {
            return;
        }
        $curTime = time();
        if(!isset($this->shopAppOptions['alert_queue_init'])) {
            $this->options->update_option('alert_queue_init', $curTime);
            add_action('admin_notices', array(&$this, 'trigger_sa_alert'));
        } else {
            if($curTime - $this->shopAppOptions['alert_queue_init'] > 604800) {
                add_action('admin_notices', array(&$this, 'trigger_sa_alert'));
            }
        }
    }

    function trigger_sa_alert() {
        ?>
            <div class="update-nag notice is-dismissible">
                <p>
                    <span style="font-size:15px;">Rich Reviews Update</span>: <?php echo __('Introducing ', 'rich-reviews') . '<strong>' . __('Organic & PPC Stars', 'rich-reviews') . '</strong> ' . __('via a Google Authorized 3rd Party Product Review Aggregator', 'rich-reviews'); ?>.
                <a href="<?php echo admin_url() . 'admin.php?page=fp_admin_shopper_approved_page'; ?>" style="margin-left:8px;">
                    <?php _e('Read more', 'rich-reviews'); ?>
                </a>
                </p>
            </div>
        <?php
        remove_action('admin_notices', array(&$this, 'trigger_sa_alert'));
    }

    function shortcode_shopper_approved_control($atts) {
        global $wpdb, $post;
        extract(shortcode_atts(
            array(
                'get' => '',
                'ids' => ''
            )
        ,$atts));

        if ($get == 'trigger') {
            $this->maybe_initialize_setup_time();
        }

        ob_start();
            if ($get != '') {
                handle_shopper_approved($get, $ids, $this->shopAppOptions, $this->parent->path);
            }
        return ob_get_clean();
    }

    function maybe_initialize_setup_time() {
        $setupTime = $this->options->get_option('init_time');

        if($setupTime == '') {
            $this->options->update_option('init_time', time()*1000);
            dump('updated');
        }
    }

    function add_edit_product_index() {
        add_submenu_page( null, 'Edit Single Product Index', 'Edit Single Product Index', 'edit_posts', 'edit_single_product_index', array( &$this, 'display_edit_single_product_index' ));
    }

	function display_handle($type = null, $id = null) {
		$stuff = $this->options->get_option();

        if ($type != null) {
            $html = '';
            switch($type) {
                case 'product':
                    if ($id != null && is_string($id)) {
                        if(isset($stuff['product_markup']) && isset($stuff['product_markup'][$id]) && $stuff['product_markup'][$id] != '') {
                            $html = $stuff['product_markup'][$id];
                        }
                    }
                    break;
                case 'merchant':
                    if(isset($stuff['merchant_markup']) && $stuff['merchant_markup'] != '') {
                        $html = $stuff['merchant_markup'];
                    }
                    break;
                default:
                    break;
            }
            return $html;
        }
		return;
	}

	function process_cache_update($data = null) {

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
        $data = $this->update_product_snippet_markup($data);

		// if(isset($markup) && $markup != null && $markup != '') {
		$data['last_update'] = date("F j, Y, g:i a");

			// $return = array(
			// 	'api_url' 		=> 	$api_url,
			// 	'merchant_markup'		=>	$markup,
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
			$data['merchant_markup'] = $markup;
		} else {
			// No file at url
			return $data;
		}

		return $data;
    }

     public function update_product_snippet_markup($data) {

        if(!isset($data['site_id']) || $data['site_id'] == '' || !isset($data['site_token']) || $data['site_token'] == '' || !isset($data['product_catalog_ids']) || empty($data['product_catalog_ids'])) {
            return $data;
        }

        $product_markup_by_id = array();
        foreach($data['product_catalog_ids'] as $id => $val) {
            $json_request = "https://www.shopperapproved.com/feeds/schema-product.php?siteid=" . $data['site_id'] . "&token=" . $data['site_token'] . "&product=" . $id;
             if(wp_remote_fopen($json_request) != false) {
                $markup = file_get_contents($json_request);
                $product_markup_by_id[$id] = $markup;
            } else {
                // No file at url
                $product_markup_by_id[$id] = null;
            }
        }

        if (isset($data['product_markup']) && is_array($data['product_markup'])) {
            $current_product_markup = $data['product_markup'];
            $product_markup_by_id = array_merge($current_product_markup, $product_markup_by_id);
        }
        $data['product_markup'] = $product_markup_by_id;
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
          $this->options->update_option('average_score', $response->average);
        }

        if(isset($response->review_count)) {
          $this->options->update_option('total_review_count', $response->review_count);
        }

        $updated_options = $this->options->get_option();

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

        $this->options->update_option('reviews_last_pulled', date("F j, Y, g:i a"));
        $stored_review_ids = $this->options->get_option('imported_review_ids');

        $total_pulled = count($stored_review_ids);
        $this->options->update_option('reviews_pulled_count', $total_pulled);

        $this->update_reviews_general_info($this->shopAppOptions);

    }

    function display_edit_single_product_index() {
        $options = $this->shopAppOptions;
        $message_class = 'updated';

        if (isset($_POST['toBeOrNotToBe']) && $_POST['toBeOrNotToBe'] == 'theQuestion') {
            unset($_POST['toBeOrNotToBe']);
            $data = $options['product_catalog_ids'][$_GET['id']];
            $newData = $data;
            $valid = true;
            foreach($_POST as $key => $val) {
                if ($val == '') {
                    $valid = false;
                } else if (isset($newData[$key])) {
                    $newData[$key] = esc_textarea($val);
                }
            }
            if (!$valid) {
                $message_text = "Please enter a valid value for each field";
                $message_class = 'error';
                @include 'views/edit_single_index.php';
                return;
            }
            $currentProductIndex = $options['product_catalog_ids'];
            $updatedProductIndex = $currentProductIndex;
            $updatedProductIndex[$_GET['id']] = $newData;
            $this->options->update_option('product_catalog_ids', $updatedProductIndex);
            $options = $this->options->get_option();
            $this->create_update_product_csv();
            $message_text = "Product listing updated and feed rewritten succesfully";
        } else if (isset($_POST['toBeOrNotToBe']) && $_POST['toBeOrNotToBe'] == 'theQuestionaire') {
            unset($_POST['toBeOrNotToBe']);
            $newData = array();

            $valid = true;
            foreach($_POST as $key => $val) {
                if($val == '') {
                    $valid = false;
                }
                $newData[$key] = esc_textarea($val);
            }
            if (!$valid) {
                $message_text = "Please enter a valid value for each field";
                $message_class = 'error';
                @include 'views/edit_single_index.php';
                return;
            }
            if (isset($options['product_catalog_ids']) && is_array($options['product_catalog_ids'])) {
                $currentProductIndex = $options['product_catalog_ids'];
            } else {
                $currentProductIndex = array();
            }
            $updatedProductIndex = $currentProductIndex;
            $product_id = $newData['id'];
            unset($newData['id']);
            $updatedProductIndex[$product_id] = $newData;
            $this->options->update_option('product_catalog_ids', $updatedProductIndex);
            $options = $this->options->get_option();
            $this->create_update_product_csv();
            $message_text = "Product listing created and feed rewritten succesfully";
            $headerString = 'Location: ' . admin_url() . 'admin.php?page=edit_single_product_index&id=' . $product_id;
            header($headerString);
            return;
        } else if (isset($_POST['deleting-listing']) && $_POST['deleting-listing'] == 'confirmed') {
            $currentProductIndex = $options['product_catalog_ids'];
            $updatedProductIndex = $currentProductIndex;
            if (isset($updatedProductIndex[$_GET['id']]) && is_array($updatedProductIndex[$_GET['id']])) {
                unset($updatedProductIndex[$_GET['id']]);
            }
            $this->options->update_option('product_catalog_ids', $updatedProductIndex);
            $this->create_update_product_csv();
            $headerString = 'Location: ' . admin_url() . 'admin.php?page=fp_admin_shopper_approved_page';
            header($headerString);
            return;
        }
        @include 'views/edit_single_index.php';
    }

    public function exportCsv($fields) {
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

    public function create_update_product_csv() {
        $this->shopAppOptions = $this->options->get_option();
        if(!isset($this->shopAppOptions['product_catalog_ids']) || empty($this->shopAppOptions['product_catalog_ids'])) {
            return;
        } else {
            // Look for file existence file will be located in root of rrShopApp folder.
            // NVM fopen should do this automatically for me
            $filePath = dirname(__FILE__) . "/rrShopAppProductFeed.csv";
            if(file_exists($filePath)) {
                unlink($filePath);
            }
            $newfile = fopen($filePath, "w");
            $fileColumns = array(
                'Product ID',
                'Product Name',
                'Description',
                'Manufacturer',
                'Product URL',
                'Image URL',
                'mpn'

            );
            fwrite($newfile, $this->exportCSV($fileColumns));
            $catalogged_ids = $this->options->get_option('product_catalog_ids');
            foreach($catalogged_ids as $id => $data) {
                $formatted_fields = array(
                    'Product ID'    => $id,
                    'Product Name'  => html_entity_decode(stripcslashes($data['name']), ENT_QUOTES),
                    'Description'   => html_entity_decode(stripcslashes($data['description']), ENT_QUOTES),
                    'Manufacturer'  => html_entity_decode(stripcslashes($data['manufacturer']), ENT_QUOTES),
                    'Product URL'   => stripcslashes($data['product_url']),
                    'Image URL'     => stripcslashes($data['image_url']),
                    'mpn'           => html_entity_decode(stripcslashes($data['mpn']), ENT_QUOTES)
                );

                fwrite($newfile, $this->exportCSV($formatted_fields));
            }

            fclose($newfile);
            $fileUrl = $this->parent->plugin_url . 'lib/rrShopApp/rrShopAppProductFeed.csv';
            $this->options->update_option('product_feed_url', $fileUrl);
        }
    }

    public function insert_shop_app_review($review, $id) {

    	$options = $this->options->get_option();
    	if(in_array($id, $options['imported_review_ids'])) {
    		return;
    	}

    	$date = $this->reformat_date($review->displaydate);

    	if($review->public == true) {
    		$review_status = 1;
    	} else {
    		$review_status = 0;
    	}

    	$text = stripcslashes($review->textcomments);

    	$newSubmission = array(
			'date_time'       => $date,
			'reviewer_name'   => $review->name,
		// 	// 'reviewer_image_id' => $newData['reviewer_image_id'],
		// 	'reviewer_email'  => $newData['reviewer_email'],
		// 	'review_title'    => $newData['review_title'],
			'review_rating'   => intval($review->Overall),
		// 	// 'review_image_id' => $newData['review_image_id'],
			'review_text'     => $text,
			'review_status'   => $review_status,
		// 	'reviewer_ip'     => $newData['reviewer_ip'],
		// 	'post_id'		  => $newData['post_id'],
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


// $shopperApprovedCache = new RRShopApp();

?>
