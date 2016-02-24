<?php



class RRShopAppOptions {

	var $defaults;

	var $options_name;

	 var $updated = FALSE;

	function __construct($parent) {

		$this->parent = $parent;
		if (isset($_POST['dinner'])) {
        $this->updated = $_POST['dinner'];
    }
		$this->options_name = $this->parent->option_name;
		$this->defaults = array(
			'api_url' 		=> 	'',
			'merchant_markup'		=>	'',
      'product_markup' => array(),
			'last_update'	=> 	'',
      'site_id' => '',
      'site_token' => '',
      'site_code' => '',
      'reviews_last_pulled' => 'not yet pulled',
      'total_review_count' => NULL,
      'reviews_pulled_count' => NULL,
      'average_score' => NULL,
      'imported_review_ids' => array(),
      'inline_review_form' => false,
      'merchant_link_text' => 'Review Us',
      'merchant_link_element_class' => '',
      'product_link_text' => 'Review Our Product',
      'product_link_element_class' => '',
      'use_rr_categories' => false,
      'product_pt_slug' => '',
      'product_catalog_ids' => array(),
      'product_feed_url' => '',
      'alert_queue_init' => null,
      'init_time' => ''
		);

		  if ($this->get_option() == FALSE) {
          $this->set_to_defaults();
      }

      $this->update_options(true);
	}


	public function set_to_defaults() {
        delete_option($this->options_name);
        foreach ($this->defaults as $key=>$value) {
            $this->update_option($key, $value);
        }
    }

	public function update_options($init = null) {
     if($init == true ) {
          foreach($this->defaults as $key => $val) {

              if(!$this->get_option($key)) {
                $this->update_option($key, $val);
              }
          }
          return;
      }
		// $this->set_to_defaults();
		if(isset($_POST["dinner"]) && $_POST['dinner'] == "served") {

        if(isset($_POST['api_url']) && $_POST['api_url'] != '') {
            if(!isset($this->parent->shopAppOptions['api_url']) || $_POST['api_url'] != $this->parent->shopAppOptions['api_url']) {
              $update = $this->parent->process_cache_update($_POST);
            } else {
              $update = $this->parent->process_cache_update($this->parent->shopAppOptions);
            }
        }

        if (isset($update) && $update != NULL){
            $data = array();
            foreach ($update as $key=>$value) {
                if ($key != 'dinner' && $key != NULL) {
                    $data[$key] = $value;
                }
            }
            // $data = $this->parent->update_site_keys($data);
            // $data = $this->update_reviews_info($data);

            $this->update_option($data);
            $_POST['dinner'] = NULL;
            $this->updated = 'wpm-update-options';
            $this->parent->shopAppOptions = $this->get_option();
        }
    }

    if (isset($_POST['Whoop']) && $_POST['Whoop'] == 'There it is') {
        $this->parent->process_reviews_pull();
    }

    if (isset($_POST['grabbing-site-code']) && $_POST['grabbing-site-code'] == 'roger') {
      if (isset($_POST['full-link-drop']) && $_POST['full-link-drop'] != '' ) {
        $code = $this->isolate_site_code($_POST['full-link-drop']);
        if($code != null) {
            $this->update_option('site_code', $code);
        }
      }
    }

    if (isset($_POST['napolean']) && $_POST['napolean'] == 'complex') {
      if (isset($_POST['merchant_link_text']) && $_POST['merchant_link_text'] != '' ) {
        $this->update_option('merchant_link_text', $_POST['merchant_link_text']);
      }
      if (isset($_POST['merchant_link_element_class'])) {
        $this->update_option('merchant_link_element_class', $_POST['merchant_link_element_class']);
      }
      if (isset($_POST['product_link_text']) && $_POST['product_link_text'] != '' ) {
        $this->update_option('product_link_text', $_POST['product_link_text']);
      }
      if (isset($_POST['product_link_element_class'])) {
        $this->update_option('product_link_element_class', $_POST['product_link_element_class']);
      }
      if (isset($_POST['inline_review_form']) && $_POST['inline_review_form'] != '') {
        $this->update_option('inline_review_form', true);
      } else {
        $this->update_option('inline_review_form', false);
      }
    }
}

public function isolate_site_code($link) {
    $urlParts = explode('?', $link);

    if(!is_array($urlParts) || !isset($urlParts[1]) || count($urlParts) !=  2) {
      return null;
    }
    $urlParamString = $urlParts[1];
    parse_str($urlParamString);
    if(isset($code)) {
        return($code);
    } else {
      return null;
    }
}


	// From metabox v1.0.6

  /**
  * Gets an option for an array'd wp_options,
  * accounting for if the wp_option itself does not exist,
  * or if the option within the option
  * (cue Inception's 'BWAAAAAAAH' here) exists.
  * @since  Version 1.0.0
  * @param  string $opt_name
  * @return mixed (or FALSE on fail)
  */
  public function get_option($opt_name = '') {
     $options = get_option($this->options_name);

     // maybe return the whole options array?
     if ($opt_name == '') {
         return $options;
     }

     // are the options already set at all?
     if ($options == FALSE) {
         return $options;
     }

     // the options are set, let's see if the specific one exists
     if (! isset($options[$opt_name])) {
         return FALSE;
     }

     // the options are set, that specific option exists. return it
     return $options[$opt_name];
  }

  /**
  * Wrapper to update wp_options. allows for function overriding
  * (using an array instead of 'key, value') and allows for
  * multiple options to be stored in one name option array without
  * overriding previous options.
  * @since  Version 1.0.0
  * @param  string $opt_name
  * @param  mixed $opt_val
  */
  public function update_option($opt_name, $opt_val = '') {
     // ----- allow a function override where we just use a key/val array

     if (is_array($opt_name) && $opt_val == '') {
         foreach ($opt_name as $real_opt_name => $real_opt_value) {
             $this->update_option($real_opt_name, $real_opt_value);
         }
     } else {
         $current_options = $this->get_option(); // get all the stored options

         // ----- make sure we at least start with blank options
         if ($current_options == FALSE) {
             $current_options = array();
         }

         // ----- now save using the wordpress function
         $new_option = array($opt_name => $opt_val);
         update_option($this->options_name, array_merge($current_options, $new_option));
     }
  }

  /**
  * Given an option that is an array, either update or add
  * a value (or data) to that option and save it
  * @since  Version 1.0.0
  * @param  string $opt_name
  * @param  mixed $key_or_val
  * @param  mixed $value
  */
  public function append_to_option($opt_name, $key_or_val, $value = NULL, $merge_values = TRUE) {
     $key = '';
     $val = '';
     $results = $this->get_option($opt_name);

     // ----- always use at least an empty array!
     if (! $results) {
         $results = array();
     }

     // ----- allow function override, to use automatic array indexing
     if ($value === NULL) {
         $val = $key_or_val;

         // if value is not in array, then add it.
         if (! in_array($val, $results)) {
             $results[] = $val;
         }
     }
     else {
         $key = $key_or_val;
         $val = $value;

         // ----- should we append the array value to an existing array?
         if ($merge_values && isset($results[$key]) && is_array($results[$key]) && is_array($val)) {
                 $results[$key] = array_merge($results[$key], $val);
         }
         else {
                 // ----- don't care if key'd value exists. we override it anyway
                 $results[$key] = $val;
         }
     }

     // use our internal function to update the option data!
     $this->update_option($opt_name, $results);
  }

  public function update_messages() {
    if ($this->updated == 'served') {
        echo '<div class="updated">The options have been successfully updated.</div>';
        $this->updated = FALSE;
    }
	}
}
