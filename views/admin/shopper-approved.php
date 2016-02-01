<?php
	$options = $this->parent->shopApp->options->get_option();
	if(isset($options['total_review_count']) && is_int($options['reviews_pulled_count'])) {
		$unpulled_reviews = intval($options['total_review_count']) - intval($options['reviews_pulled_count']);
		$unpulled_reviews = (string)$unpulled_reviews;
	} else {
		$unpulled_reviews = null;
	}



?>
		<div class="rr_shortcode_container shop-app-tab">
			<!-- check if this is already set -->
			<div class="shop-app-info active">
				<h3>Already registered with Shopper Approved?</h3>
				<button class="button toggle-shop-app-config">
					Configure Shopper Approved Extension
				</button>
			</div>
			<div class="shop-app-init">
				<form id="shopAppAdmin"method="post" action="">
					<input type="hidden" name="dinner" value="served" />
					<div class="label-container one-fifth">
						<label for="api_url">
							Enter API Url:
						</label>
					</div>
					<br />
					<div class="input-container" >
						<input type="text" name="api_url" placeholder="API URL" <?php if($options['api_url'] != null) { echo 'value="' . $options['api_url'] . '"';} ?> />
					</div>
					<div class="clear"></div>
					<br />
					<input type="submit" class="button" id="submit-api-url" value="Submit Url" style="float:right;" />
					<div class="clear"></div>
				</form>
			</div>

			<div class="clear"></div>
			<br />
				<?php
					if((isset($options['site_id']) && isset($options['site_token'])) && ($options['site_id'] != '' && $options['site_token'] != '')) {
						?>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="api_url" style="float:right;font-size:13px;">
								Extracted Site ID:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="site_id" style="width: 100%;float:left;" placeholder="API URL" style="vertical-align:bottom;" <?php echo 'value="' . $options['site_id'] . '"'; ?> disabled />
						</div>
						<div class="clear"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="api_url" style="float:right;font-size:13px;">
								Extracted Site Token:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="site_token" style="width: 100%;float:left;" placeholder="API URL" style="vertical-align:bottom;" <?php echo 'value="' . $options['site_token'] . '"';?> disabled />
						</div>
						<div class="clear"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="total_review_count" class="one-third" style="float:right;font-size:13px;">
								Total Shopper Approved Reviews:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="total_review_count" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['total_review_count'] != null) { echo 'value="' . $options['total_review_count'] . '"';} ?> disabled />
						</div>
						<div class="clear"></div>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="average_score" class="one-third" style="float:right;font-size:13px;">
								Average Score:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="average_score" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['average_score'] != null) { echo 'value="' . $options['average_score'] . '"';} ?> disabled />
						</div>
						<div class="clear"></div>

						<h2>Aggregate Snippet Settings</h2>
						<hr>
						<div class="label-container one-fifth" style="width:30%; float:left;">
							<label for="html-markup" style="float:right;font-size:13px;">
								Shopper Approved Markup:
								<div style="font-size: 11px; font-weight: 400; margin-top: 8px; font-style: italic;">Use the shortcode <code style="font-style: normal; font-size: 11px;">[RICH_REVIEWS_SNIPPET category="shopperApproved"]</code> to output this markup</div>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<code name="Shopper Approved" placeholder="API Key" rows="10" cols="100" style="overflow:scroll;width:100%;float:left;" >
							<?php if($options['markup'] != null) { echo htmlspecialchars($options['markup']);} ?>
							</code>
						</div>
						<div class="clear"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="last_update" class="one-third" style="float:right;font-size:13px;">
								Last Updated:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="last_update" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['last_update'] != null) { echo 'value="' . $options['last_update'] . '"';} ?>/>
						</div>
						<div class="clear"></div>
						<br/>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<input type="submit" form="shopAppAdmin" class="button" id="force-update" style="float:right;" value="Manual Update"/>
						</div>
						<div class="clear"></div>
						<br />

						<h2>Shopper Approved Shortcode Options</h2>
						<hr>
						<form name="shopper-approved-shortcode-options" method="post">
							<input type="hidden" name="napolean" value="complex" />
							<div class="label-container one-fifth" style="width:30%;float:left;">
								<label for="link_text" class="one-third" style="float:right;font-size:13px;">
									Link Text:
								</label>
							</div>
							<div class="input-container two-thirds" style="width:66%;float:right;">
								<input type="text" name="link_text" class="two-thirds" style="width:100%;float:left;" <?php if($options['link_text'] != null) { echo 'value="' . $options['link_text'] . '"';} ?>/>
							</div>
							<div class="clear"></div>
							<div class="label-container one-fifth" style="width:30%;float:left;">
								<label for="link_element_class" class="one-third" style="float:right;font-size:13px;">
									Link Element Class:
								</label>
							</div>
							<div class="input-container two-thirds" style="width:66%;float:right;">
								<input type="text" name="link_element_class" class="two-thirds" style="width:100%;float:left;" <?php if($options['link_element_class'] != null) { echo 'value="' . $options['link_element_class'] . '"';} ?>/>
							</div>
							<div class="clear"></div>
							<div class="label-container one-fifth" style="width:30%;float:left;">
								<label for="inline_review_form" class="one-third" style="float:right;text-align:right;font-size:13px;">
									Display Review Inline:<br/>
									<span style="font-size:10px;">(default shows form in modal)</span>
								</label>
							</div>
							<div class="input-container two-thirds" style="width:66%;float:right;">
								<input type="checkbox" name="inline_review_form" class="two-thirds" <?php if($options['inline_review_form']) { echo 'checked';} ?>/>
							</div>
							<div class="clear"></div>
							<br/>
							<input type="submit" value="Update Shortcode Options" class="button" />
						</form>
						<h2>Pull Reviews</h2>
						<hr>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="reviews_last_pulled" class="one-third" style="float:right;font-size:13px;">
								Last Pulled:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="reviews_last_pulled" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['reviews_last_pulled'] != null) { echo 'value="' . $options['reviews_last_pulled'] . '"';} ?> disabled />
						</div>
						<div class="clear"></div>
						<?php
						if(isset($options['reviews_pulled_count']) && $options['reviews_last_pulled'] != 'not yet pulled') {
							?>
							<div class="label-container one-fifth" style="width:30%;float:left;">
								<label for="unpulled_reviews" class="one-third" style="float:right;font-size:13px;">
									New Unpulled Reviews:
								</label>
							</div>
							<div class="input-container two-thirds" style="width:66%;float:right;">
								<input type="text" name="reviews_last_pulled" disabled class="two-thirds" style="width:100%;float:left;" <?php if($unpulled_reviews != null) { echo 'value="' . $unpulled_reviews . '"';} ?> disabled />
							</div>
							<div class="clear"></div>
							<?php
						}
						?>
						<form name="pullReviewsButton" method="post" action="" >
							<input type="hidden" name="Whoop" value="There it is" />
							<div class="input-container one-third" style="width:33%;float:left;">
								<div style="width: 100%;float:left;">
									<input type="submit" class="button left" value="Pull Reviews" />
								</div>
							</div>
						</form>

			<?php	} ?>
					</div>



						<style>
							.postbox-container {
								float: none;
							}
						</style>

