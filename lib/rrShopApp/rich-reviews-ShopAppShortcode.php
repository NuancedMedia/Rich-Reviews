<?php

function handle_shopper_approved($switch, $options, $path) {
	global $wpdb, $post;

	switch($switch) {
		case 'trigger':
			output_trigger_script($options);
			break;
		case 'link':
			output_review_link($options);
			break;
		case 'schema':
			output_review_structured_snippet($options);
	}

}

function output_trigger_script($options) {

	$option = false;
	$sub = 'rate';
	if(isset($options['inline_review_form']) && $options['inline_review_form']) {
		$option = true;
	}

	if($option) {
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
}

function output_review_link($options) {

	$link_text = 'Review Us';
	if (isset($options['link_text']) && $options['link_text'] != '') {
		$link_text = $options['link_text'];
	}
	if(isset($options['site_id']) && $options['site_id'] != '' && isset($options['site_token']) && $options['site_token'] != '') {
		$link_url = 'http://www.shopperapproved.com/surveys/sale.php?id=' . $options['site_id'] . '&token=' . $options['site_token'];

		?>
			<a class="button" href="<?php echo $link_url; ?>" ><?php echo $link_text; ?></a>
		<?php
	}
}

function output_review_structured_snippet($options) {
	$tempRR = new RRShopApp();

	if(isset($tempRR->shopAppOptions['markup']) && $tempRR->shopAppOptions['markup'] != '') {
		echo $tempRR->display_handle();
		return;
	}
	return;
}
