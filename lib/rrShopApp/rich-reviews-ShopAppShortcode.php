<?php

function handle_shopper_approved($switch, $ids, $options, $path) {
	global $wpdb, $post;

	switch($switch) {
		case 'trigger':
			output_trigger_script($options, $ids);
			break;
		case 'merchant-link':
			output_merchant_review_link($options);
			break;
		case 'product-link':
			output_product_review_link($options);
			break;
		case 'merchant-schema':
			output_merchant_review_structured_snippet($options);
			break;
		case 'product-schema':
			if ($ids != '') {
				output_product_review_structured_snippet($options, $ids);
			}
			break;
	}

}

function output_trigger_script($options, $ids) {
	$option = false;
	$sub = 'rate';
	if (isset($options['inline_review_form']) && $options['inline_review_form']) {
		$option = true;
	}

	if ($option) {
		$sub = 'inline';
		?>
			<div id="outer_shopper_approved"></div>
			<style>
				#sa_header_img {
					display: block;
				}
			</style>
		<?php
	}
	?>
		<!-- <pre> -->
			<script type="text/javascript">
				var sa_values = { "site":<?php echo $options['site_id']; ?> };
				function saLoadScript(src) {
					var js = window.document.createElement("script");
					js.src = src; js.type = "text/javascript";
					document.getElementsByTagName("head")[0].appendChild(js);
				}

				var d = new Date();
				if (d.getTime() - 172800000 > 1453483499000)
					saLoadScript("//www.shopperapproved.com/thankyou/<?php echo $sub; ?>/<?php echo $options['site_id']; ?>.js");
				else
					saLoadScript("//direct.shopperapproved.com/thankyou/<?php echo $sub; ?>/<?php echo $options['site_id']; ?>.js?d=" + d.getTime());
			</script>
		<!-- </pre> -->
	<?php
	if (isset($options['product_catalog_ids']) && !empty($options['product_catalog_ids']) &&  $ids != '') {
		$product_ids = explode(',', $ids);
		$ids_object = '';
		foreach($product_ids as $id) {
			if(isset($options['product_catalog_ids'][$id]) && is_array($options['product_catalog_ids'][$id]) && isset($options['product_catalog_ids'][$id]['name'])) {
				$ids_object .= '"' . $id . '":"' . $options['product_catalog_ids'][$id]['name']  . '", ';
			}
		}
		?>
		<script type="text/javascript">
			/* Include all products in the object below 'product id':'Product Name' */
			var sa_products = {<?php echo $ids_object; ?>};
		</script>
		<?php
	}
}

function output_merchant_review_link($options) {

	$link_text = 'Review Us';
	$link_class = '';
	if (isset($options['merchant_link_text']) && $options['merchant_link_text'] != '') {
		$link_text = $options['merchant_link_text'];
	}
	if (isset($options['merchant_link_class']) && $options['merchant_link_class'] != '') {
		$link_class = $options['merchant_link_class'];
	}
	if(isset($options['site_id']) && $options['site_id'] != '' && isset($options['site_token']) && $options['site_code'] != '') {
		$link_url = 'http://www.shopperapproved.com/surveys/full.php?id=' . $options['site_id'] . '&code=' . $options['site_code'];

		?>
			<a class="button <?php echo $link_class; ?>" href="<?php echo $link_url; ?>" ><?php echo $link_text; ?></a>
		<?php
	}
}

function output_product_review_link($options) {

	if(!isset($options['product_catalog_ids']) || empty($options['product_catalog_ids'])) {
		return;
	}
	$product_id_string = '';

	foreach($options['product_catalog_ids'] as $id => $data) {
		$product_id_string .= 'products[]=' . $id . '&';
	}
	$link_text = 'Review Our Product';
	$link_class = '';
	if (isset($options['product_link_text']) && $options['product_link_text'] != '') {
		$link_text = $options['product_link_text'];
	}
	if (isset($options['product_link_class']) && $options['product_link_class'] != '') {
		$link_class = $options['product_link_class'];
	}
	if(isset($options['site_id']) && $options['site_id'] != '' && isset($options['site_code']) && $options['site_code'] != '') {
		$link_url = 'http://www.shopperapproved.com/surveys/sale.php?' . $product_id_string . 'id=' . $options['site_id'] . '&code=' . $options['site_code'];

		?>
			<a class="button <?php echo $link_class; ?>" href="<?php echo $link_url; ?>" ><?php echo $link_text; ?></a>
		<?php
	}
}

function output_merchant_review_structured_snippet($options) {
	$tempRR = new RRShopApp();

	if(isset($tempRR->shopAppOptions['merchant_markup']) && $tempRR->shopAppOptions['merchant_markup'] != '') {
		echo $tempRR->display_handle('merchant');
		return;
	}
	return;
}

function output_product_review_structured_snippet($options, $ids = null) {
	$tempRR = new RRShopApp();

	if ($ids != null && $ids != '') {
		$id_array = explode(',', $ids);
		if ( !is_array($id_array) ) {
			return;
		}
	}

	if (isset($tempRR->shopAppOptions['product_markup'])) {
		foreach($id_array as $id) {
			echo $tempRR->display_handle('product', $id);
		}
	}
	return;
}
