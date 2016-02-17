<?php

        function fetch_unique_from_rr_categories() {
            global $richReviews, $wpdb;

            $query = "SELECT review_category, COUNT(*) as reviewCount FROM $richReviews->sqltable GROUP BY review_category";
            $results = $wpdb->get_results($query);
            $rr_categories = array();
            foreach($results as $result) {
                $rr_categories[$result->review_category] = array(
                    'name'          => '',
                    'description'   => '',
                    'manufacturer'  => '',
                    'product_url'   => '',
                    'image_url'     => '',
                    'mpn'           => '',
                    'review_count'  => $result->reviewCount  //maybe we'll be removing this to be handled by SA
                );
            }
            return $rr_categories;
        }

        // function update_category_review_count() {
        //     global $richReviews, $wpdb;
        //     $rr_categories = $richReviews->shopApp->shopAppOptions['product_catalog_ids'];
        //     foreach($rr_categories as $id => $data) {
        //         $query = "SELECT COUNT(review_category=$id) FROM $richReviews->sqltable";
        //         $results = $wpdb->get_results($query);
        //         dump($query);
        //     }
        // }

        function build_product_catalog_from_product_posts($posts) {

            $incoming_catalog_ids = array();

            if (is_array($posts) && !empty($posts)) {
                foreach($posts as $post) {
                    $product_url = get_the_permalink($post->ID);
                    $featured_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
                    $data = array(
                        'name'          => $post->post_title,
                        'description'   => $post->post_content,
                        'manufacturer'  => '',
                        'product_url'   => $product_url,
                        'image_url'     => $featured_image_url,
                        'mpn'           => (string) $post->ID,
                        'review_count'  => null   //maybe we should remove this all together
                    );

                    $incoming_catalog_ids[$post->post_name] = $data;
                }
            }
            return $incoming_catalog_ids;
        }


		$path = realpath(null);
        $parts = explode('wp-content', $path);
        if (count($parts) == 2) {
            $load_path = $parts[0] . 'wp-load.php';
        } else {
            die();
        }
        require_once($load_path);

        $cur_user = wp_get_current_user();

        if (!$cur_user->ID || !current_user_can('administrator')) {
        	header($_SERVER["SERVER_PROTOCOL"] . " 403.2 Read Access Denied");
        	die();
        }


		global $wpdb, $richReviews;

        if (isset($_POST['updating_product_catalog']) && $_POST['updating_product_catalog'] == 'updatingCatalog') {
            if (isset($_POST['use_rr_categories']) && $_POST['use_rr_categories'] == 'on') {

                // Simply eliminate the headache of both scripts running at once, might not be an issue...
                $richReviews->shopApp->options->update_option('use_rr_categories', 'checked');
                $richReviews->shopApp->options->update_option('product_pt_slug', '');

                // Build product catalog from Rich Reviews catgories
                $results = fetch_unique_from_rr_categories();
                $options = $richReviews->shopApp->shopAppOptions;

                if(!isset($options['product_catalog_ids']) || empty($options['product_catalog_ids'])) {
                    $richReviews->shopApp->options->update_option('product_catalog_ids', $results);
                } else {
                    $currentIndex = $options['product_catalog_ids'];
                    $updatedIndex = array_merge($results, $currentIndex);
                    $richReviews->shopApp->options->update_option('product_catalog_ids', $updatedIndex);
                }
            } else if (isset($_POST['product_pt_slug']) && $_POST['product_pt_slug'] != '') {

                // Simply eliminate the headache of both scripts running at once, might not be an issue...
                $richReviews->shopApp->options->update_option('product_pt_slug', $_POST['product_pt_slug']);
                $richReviews->shopApp->options->update_option('use_rr_categories', false);

                $products = get_posts(array(
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'post_type' => $_POST['product_pt_slug']
                ));

                // Build product catalog from product posts
                $results = build_product_catalog_from_product_posts($products);
                $options = $richReviews->shopApp->shopAppOptions;

                if(!isset($options['product_catalog_ids']) || empty($options['product_catalog_ids'])) {
                    $richReviews->shopApp->options->update_option('product_catalog_ids', $results);
                } else {
                    $currentIndex = $options['product_catalog_ids'];
                    $updatedIndex = array_merge($results, $currentIndex);
                    $richReviews->shopApp->options->update_option('product_catalog_ids', $updatedIndex);
                }
            }
            $richReviews->shopApp->create_update_product_csv();
        }

        $backToOptions = "Location: " . admin_url() . "admin.php?page=fp_admin_shopper_approved_page";
        header($backToOptions);
        // die();













