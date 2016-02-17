<?php
	$options = $this->parent->shopApp->options->get_option();
	if(isset($options['total_review_count']) && is_int($options['reviews_pulled_count'])) {
		$unpulled_reviews = intval($options['total_review_count']) - intval($options['reviews_pulled_count']);
		$unpulled_reviews = (string)$unpulled_reviews;
	} else {
		$unpulled_reviews = null;
	}

	$init_active = '';
	$info_active = '';

	if(isset($options['api_url']) && $options['api_url'] != '') {
		$init_active = 'active';
	} else {
		$info_active = 'active';
	}

?>
		<div class="rr_shortcode_container shop-app-tab">
			<!-- check if this is already set -->
			<div class="shop-app-info <?php echo $info_active; ?> options-section">
				<h3><?php _e('Already registered with Shopper Approved?', 'rich-reviews'); ?></h3>
				<button class="button toggle-shop-app-config">
					<?php _e('Configure Shopper Approved Extension', 'rich-reviews'); ?>
				</button>
			</div>
			<div class="shop-app-init <?php echo $init_active; ?> options-section">
				<form id="shopAppAdmin"method="post" action="">
					<input type="hidden" name="dinner" value="served" />
					<div class="label-container one-fifth">
						<label for="api_url" style="font-size:21px;">
							<?php _e('Enter API Url:', 'rich-reviews'); ?>
						</label>
					</div>
					<br />
					<div class="input-container" >
						<input type="text" name="api_url" placeholder="<?php _e('API URL', 'rich-reviews'); ?>" <?php if($options['api_url'] != null) { echo 'value="' . $options['api_url'] . '"';} ?> />
					</div>
					<div class="clear"></div>
					<br />
					<input type="submit" class="button" id="submit-api-url" value="<?php _e('Submit Url', 'rich-reviews'); ?>" style="float:right;" />
					<div class="clear"></div>

			<br />
				<?php
					if((isset($options['site_id']) && isset($options['site_token'])) && ($options['site_id'] != '' && $options['site_token'] != '')) {
						?>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="api_url" style="float:right;">
								<?php _e('Extracted Site ID:', 'rich-reviews'); ?>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="site_id" style="width: 100%;float:left;" placeholder="<?php _e('API URL', 'rich-reviews'); ?>" style="vertical-align:bottom;" <?php echo 'value="' . $options['site_id'] . '"'; ?> disabled />
						</div>
						<div class="clear input-break"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="api_url" style="float:right;">
								<?php _e('Extracted Site Token:', 'rich-reviews'); ?>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="site_token" style="width: 100%;float:left;" placeholder="API URL" style="vertical-align:bottom;" <?php echo 'value="' . $options['site_token'] . '"';?> disabled />
						</div>
						<div class="clear input-break"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="total_review_count" class="one-third" style="float:right;">
								<?php _e('Total Shopper Approved Reviews:', 'rich-reviews'); ?>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="total_review_count" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['total_review_count'] != null) { echo 'value="' . $options['total_review_count'] . '"';} ?> disabled />
						</div>
						<div class="clear input-break"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="average_score" class="one-third" style="float:right;">
								<?php _e('Average Score:', 'rich-reviews'); ?>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="average_score" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['average_score'] != null) { echo 'value="' . $options['average_score'] . '"';} ?> disabled />
						</div>
						<div class="clear input-break"></div>
						</form>
					</div>

					<div class="clear"></div>

					<div class="options-section">
						<h2><?php _e('Aggregate Snippet Settings', 'rich-reviews'); ?></h2>
						<hr>
						<div class="label-container one-fifth" style="width:30%; float:left;">
							<label for="html-markup" style="float:right;">
								<?php _e('Shopper Approved Markup:', 'rich-reviews'); ?>
								<div style="font-size: 11px; font-weight: 400; margin-top: 8px; font-style: italic;"><?php _e('Use the shortcode', 'rich-reviews'); ?> <code style="font-style: normal; font-size: 11px;">[RR_SHOPPER_APPROVED get="schema"]</code> <?php _e('to output this markup', 'rich-reviews'); ?></div>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<code name="Shopper Approved" placeholder="API Key" rows="10" cols="100" style="overflow:scroll;width:100%;float:left;" >
							<?php if($options['markup'] != null) { echo htmlspecialchars($options['markup']);} ?>
							</code>
						</div>
						<div class="clear input-break"></div>
						<br />
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="last_update" class="one-third" style="float:right;">
								<?php _e('Last Updated:', 'rich-reviews'); ?>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="last_update" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['last_update'] != null) { echo 'value="' . $options['last_update'] . '"';} ?>/>
						</div>
						<div class="clear input-break"></div>
						<br/>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<input type="submit" form="shopAppAdmin" class="button" id="force-update" value="<?php _e('Manual Update', 'rich-reviews'); ?>"/>
						</div>
						<div class="clear"></div>
					</div>
					<div class="options-section">
						<h2><?php _e('Shopper Approved Shortcode Options', 'rich-reviews'); ?></h2>
						<hr>

						<form name="shopper-approved-shortcode-options" method="post">
							<input type="hidden" name="napolean" value="complex" />
							<div class="link-options">
								<?php if (isset($options['site_code']) && $options['site_code'] !=  '') {
									?>
										<div class="merchant-options">
											<h3>Merchant Review Link</h3>
											<div class="label-container one-fifth" style="width:30%;float:left;">
												<label for="reviews-link-shortcode" class="one-third" style="float:right;">
													<?php _e('Output Link to Full Review', 'rich-reviews'); ?>:
												</label>
											</div>
											<div class="input-container two-thirds" style="width:66%;float:right;">
												<span class="two-thirds" style="width:100%;float:left;">
													[RR_SHOPPER_APPROVED get="merchant-link"]
												</span>
											</div>
											<div class="clear input-break"></div>
											<br/>
											<div class="label-container one-fifth" style="width:30%;float:left;">
												<label for="merchant_link_text" class="one-third" style="float:right;">
													<?php _e('Link Text', 'rich-reviews'); ?>:
												</label>
											</div>
											<div class="input-container two-thirds" style="width:66%;float:right;">
												<input type="text" name="merchant_link_text" class="two-thirds" style="width:100%;float:left;" <?php if($options['merchant_link_text'] != null) { echo 'value="' . $options['merchant_link_text'] . '"';} ?>/>
											</div>
											<div class="clear input-break"></div>
											<br />

											<div class="label-container one-fifth" style="width:30%;float:left;">
												<label for="merchant_link_element_class" class="one-third" style="float:right;">
													<?php _e('Link Element Class', 'rich-reviews'); ?>:
												</label>
											</div>
											<div class="input-container two-thirds" style="width:66%;float:right;">
												<input type="text" name="merchant_link_element_class" class="two-thirds" style="width:100%;float:left;" <?php if($options['merchant_link_element_class'] != null) { echo 'value="' . $options['merchant_link_element_class'] . '"';} ?>/>
											</div>
											<div class="clear input-break"></div>
										</div>
										<div class="product-options">
											<h3>Product Review Link</h3>
											<div class="label-container one-fifth" style="width:30%;float:left;">
												<label for="reviews-link-shortcode" class="one-third" style="float:right;">
													<?php _e('Output Link to Full Review', 'rich-reviews'); ?>:
												</label>
											</div>
											<div class="input-container two-thirds" style="width:66%;float:right;">
												<span class="two-thirds" style="width:100%;float:left;">
													[RR_SHOPPER_APPROVED get="product-link"]
												</span>
											</div>
											<div class="clear input-break"></div>
											<br/>
											<div class="label-container one-fifth" style="width:30%;float:left;">
												<label for="product_link_text" class="one-third" style="float:right;">
													<?php _e('Link Text', 'rich-reviews'); ?>:
												</label>
											</div>
											<div class="input-container two-thirds" style="width:66%;float:right;">
												<input type="text" name="product_link_text" class="two-thirds" style="width:100%;float:left;" <?php if($options['product_link_text'] != null) { echo 'value="' . $options['product_link_text'] . '"';} ?>/>
											</div>
											<div class="clear input-break"></div>
											<br />

											<div class="label-container one-fifth" style="width:30%;float:left;">
												<label for="product_link_element_class" class="one-third" style="float:right;">
													<?php _e('Link Element Class', 'rich-reviews'); ?>:
												</label>
											</div>
											<div class="input-container two-thirds" style="width:66%;float:right;">
												<input type="text" name="product_link_element_class" class="two-thirds" style="width:100%;float:left;" <?php if($options['product_link_element_class'] != null) { echo 'value="' . $options['product_link_element_class'] . '"';} ?>/>
											</div>
											<div class="clear input-break"></div>
										</div>
									<?php
								} else {
									?>
										<h4><?php _e('To Enable Link Shortcode Options, please copy the "Link to Full Survey" from the Merchant Reviews Survey Options tab of Shopper Approved and enter it below.', 'rich-reviews'); ?></h4>
										<form name="site-code-grab" method="post">
											<input type="hidden" name="grabbing-site-code" value="roger" />
											<input name="full-link-drop" style="width:80%;margin:auto;" />
											<input type="submit" class="button right" value="<?php _e('Submit Link', 'rich-reviews'); ?>" />
										</form>
									<?php
								}
								?>

							</div>
							<br />
							<div class="trigger-options">
								<h3>Review Trigger</h3>
								<div class="label-container one-fifth" style="width:30%;float:left;">
									<label for="reviews-trigger-shortcode" class="one-third" style="float:right;">
										<?php _e('Trigger Review Form', 'rich-reviews'); ?>:
									</label>
								</div>
								<div class="input-container two-thirds" style="width:66%;float:right;">
									<span class="two-thirds" style="width:100%;float:left;">
										[RR_SHOPPER_APPROVED get="trigger"]
									</span>
								</div>
								<div class="clear input-break"></div>
								<br/>
								<div class="label-container one-fifth" style="width:30%;float:left;">
									<label for="inline_review_form" class="one-third" style="float:right;text-align:right;">
										<?php _e('Display Review Inline', 'rich-reviews'); ?>:<br/>
										<span style="font-size:10px;">(<?php _e('default shows form in modal)', 'rich-reviews'); ?></span>
									</label>
								</div>
								<div class="input-container two-thirds" style="width:66%;float:right;">
									<input type="checkbox" name="inline_review_form" class="two-thirds" <?php if($options['inline_review_form']) { echo 'checked';} ?>/>
								</div>
								<br />
								<div class="clear input-break"></div>
							</div>
							<br/>
							<input type="submit" value="<?php _e('Update Shortcode Options', 'rich-reviews'); ?>" class="button" />
						</form>
					</div>
					<div class="options-section">
						<h2><?php _e('Pull Reviews', 'rich-reviews'); ?></h2>
						<hr>
						<div class="label-container one-fifth" style="width:30%;float:left;">
							<label for="reviews_last_pulled" class="one-third" style="float:right;">
								<?php _e('Last Pulled:', 'rich-reviews'); ?>
							</label>
						</div>
						<div class="input-container two-thirds" style="width:66%;float:right;">
							<input type="text" name="reviews_last_pulled" disabled class="two-thirds" style="width:100%;float:left;" <?php if($options['reviews_last_pulled'] != null) { echo 'value="' . $options['reviews_last_pulled'] . '"';} ?> disabled />
						</div>
						<div class="clear"></div>
						<br />
						<?php
						if(isset($options['reviews_pulled_count']) && $options['reviews_last_pulled'] != 'not yet pulled') {
							?>
							<div class="label-container one-fifth" style="width:30%;float:left;">
								<label for="unpulled_reviews" class="one-third" style="float:right;">
									<?php _e('New Unpulled Reviews:', 'rich-reviews'); ?>
								</label>
							</div>
							<div class="input-container two-thirds" style="width:66%;float:right;">
								<input type="text" name="reviews_last_pulled" disabled class="two-thirds" style="width:100%;float:left;" <?php if($unpulled_reviews != null) { echo 'value="' . $unpulled_reviews . '"';} ?> disabled />
							</div>
							<br />
							<div class="clear"></div>
							<?php
						}
						?>
						<form name="pullReviewsButton" method="post" action="" >
							<input type="hidden" name="Whoop" value="There it is" />
							<div class="input-container one-third" style="width:33%;float:left;">
								<div style="width: 100%;float:left;">
									<input type="submit" class="button left" value="<?php _e('Pull Reviews', 'rich-reviews'); ?>" />
								</div>
							</div>
						</form>
						<div class="clear"></div>
					</div>
					<div class="options-section">
						<h2><?php _e('Export Current Reviews', 'rich-reviews'); ?></h2>
						<hr>
						<p>
							<?php _e('Press the button below to download a csv file of all of the reviews currently in your Rich Reviews. This way you can send these reviews to Shopper Approved and have them imported to your Shopper Approved Merchant or Product rating. Further instruction on how to do this can be found', 'rich-reviews'); ?> <a href=""><?php _e('here', 'rich-reviews'); ?></a>.
						</p>
						<a href="<?php echo $this->parent->plugin_url; ?>richreviews-download-script.php?download=csv" class="button left"><?php _e('Download Reviews', 'rich-reviews'); ?></a>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
					<div class="options-section">
						<h2><?php _e('Integrate Product Reviews', 'rich-reviews'); ?></h2>
						<hr>
						<form id="productReviewsIntegration" action="<?php echo $this->parent->plugin_url; ?>richreviews-update-product-index.php" method="post">
							<input type="hidden" name="updating_product_catalog" value="updatingCatalog" />
							<p>
								<?php _e('Enter the slug for the post type under which your products are categorized, or select to use your Rich Reviews category structure (only one can be used for initialization). This will help to generate the needed xml file to communicate product listings and the associated review information to Shopper Approved. This file will be updated with new product listing once a day. (After initial configuration, you can force an update by pressing the button below)'); ?>
							</p>
							<div class="initialize-options">
								<div class="label-container one-fifth" style="width:40%;float:left;padding-top:3px;">
									<label for="product_pt_slug" class="one-third" style="float:right;margin-right:5px;">
										<?php _e('Product post type slug:', 'rich-reviews'); ?>
									</label>
								</div>
								<div class="input-container two-thirds" style="width:60%;float:right;">
									<input type="text" name="product_pt_slug" class="two-thirds" style="width:100%;float:left;" <?php if(isset($options['product_pt_slug']) && $options['product_pt_slug'] != '') { echo 'value="' . $options['product_pt_slug'] . '"';} ?>/>
								</div>
								<div class="clear"></div>
								<br />
								<h3 style="text-align:center;"><span class="line"></span>  OR  <span class="line"></span></h3>
								<br/>
								<div class="label-container one-fifth" style="width:40%;float:left;">
									<label for="use_rr_categories" class="one-third" style="float:right;margin-right:5px;">
										<?php _e('Use Rich Reviews Categories:', 'rich-reviews'); ?>
									</label>
								</div>
								<div class="input-container two-thirds" style="width:60%;float:right;">
									<input type="checkbox" name="use_rr_categories" class="two-thirds" <?php if(isset($options['use_rr_categories']) && $options['use_rr_categories'] == 'checked') { echo 'checked';} ?>/>
								</div>
								<script type="text/javascript">
									jQuery(function() {
										jQuery('input[name="product_pt_slug"]').keyup(function() {
											if (jQuery(this).val() != '') {
												jQuery('input[name="use_rr_categories"]').prop("checked", false);
											}
										});
										jQuery('input[name="use_rr_categories"]').change(function() {
											if (jQuery(this).val() == 'on') {
												jQuery('input[name="product_pt_slug"]').val('');
											}
										});
									});
								</script>
								<div class="clear"></div>
							</div>
							<p>
								<?php _e('Update and Download your Shopper Approved product record. Pleas double check that the file is output and formatted to the requirements described by Shopper Approved in the Product Reviews "Integration Options" tab. For more information on this process, refer to you Shopper Approved Dashboard ', 'rich-reviews'); ?> <a href=""><?php _e('here', 'rich-reviews'); ?></a>.
							</p>
							<input type="submit" class="button" value="<?php _e('Update Product Index', 'rich-reviews'); ?>" form="productReviewsIntegration" />
							<a href="<?php echo $this->parent->plugin_url; ?>richreviews-download-product-index.php?type=csv" class="button left"><?php _e('Download Product Index', 'rich-reviews'); ?></a>
							<div class="clear"></div>
						</form>
						<br/>
						<div class="label-container one-fifth" style="width:25%;float:left;padding-top:5px;">
							<label for="product_feed_url" class="one-third" >
								<?php _e('Product Feed URL', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="input-container two-thirds" style="width:75%;float:right;">
							<input type="text" name="product_feed_url" class="one" value="<?php if(isset($options['product_feed_url'])) { echo $options['product_feed_url'];} ?>" disabled/>
						</div>
						<div class="clear"></div>
						<br/>
						<?php
							if (isset($options['product_catalog_ids']) && !empty($options['product_catalog_ids'])) {
								?>
								<div class="one">
									<h2><?php echo _e('Product Catalog Ids', 'rich-reviews'); ?></h2>
									<hr>
									<?php
										$count = 0;
										foreach($options['product_catalog_ids'] as $catalogged_id => $data) {
											$count++;
										?>
											<div class="single-product-info">
												<a href="<?php echo admin_url() . 'admin.php?page=edit_single_product_index&id=' . $catalogged_id; ?>"  class="product-edit-link right"><i class="dashicon-edit"></i> <?php _e('Edit', 'rich-reviews'); ?></a>
												<summary>
												<h3>
													<?php _e('ID', 'rich-reviews'); ?>: <?php echo $catalogged_id; ?>
												</h3>
												<details>
													<span class="single-product-detail"><?php _e('Name', 'rich-reviews'); ?>: <?php if ($data['name'] != '') { echo '<span class="good">' . __('SET', 'rich-reviews') . '</span>';} else { echo '<span class="bad">' . __('UNSET', 'rich-reviews') . '</span>';} ?></span>
													<span class="single-product-detail"><?php _e('Description', 'rich-reviews'); ?>: <?php if ($data['description'] != '') { echo '<span class="good">' . __('SET', 'rich-reviews') . '</span>';} else { echo '<span class="bad">' . __('UNSET', 'rich-reviews') . '</span>';} ?></span>
													<span class="single-product-detail"><?php _e('Manufacturer', 'rich-reviews'); ?>: <?php if ($data['manufacturer'] != '') { echo '<span class="good">' . __('SET', 'rich-reviews') . '</span>';} else { echo '<span class="bad">' . __('UNSET', 'rich-reviews') . '</span>';} ?></span>
													<span class="single-product-detail"><?php _e('Product Url', 'rich-reviews'); ?>: <?php if ($data['product_url'] != '') { echo '<span class="good">' . __('SET', 'rich-reviews') . '</span>';} else { echo '<span class="bad">' . __('UNSET', 'rich-reviews') . '</span>';} ?></span>
													<span class="single-product-detail"><?php _e('Image Url', 'rich-reviews'); ?>: <?php if ($data['image_url'] != '') { echo '<span class="good">' . __('SET', 'rich-reviews') . '</span>';} else { echo '<span class="bad">' . __('UNSET', 'rich-reviews') . '</span>';} ?></span>
													<span class="single-product-detail"><?php _e('Manufacturer Part Number', 'rich-reviews'); ?>: <?php if ($data['mpn'] != '') { echo '<span class="good">' . __('SET', 'rich-reviews') . '</span>';} else { echo '<span class="bad">' . __('UNSET', 'rich-reviews') . '</span>';} ?></span>
												</details>
												</summary>
											</div>
										<?php
											if($count%3 == 0) {
												echo '<div class="clear"></div>';
											}
										}
									?>
									<div class="clear"></div>
									<br />
									<a href="<?php echo admin_url() . 'admin.php?page=edit_single_product_index&new=true'; ?>" class="button"><?php _e('Add New Product Listing', 'rich-reviews'); ?></a>
								</div>
								<?php
							}
						?>
					</div>
					<div class="clear"></div>

			<?php	} else { ?>
				</form>
			</div>

			<div class="clear"></div>
			<h2>Shopper Approved</h2>

			<div class="sa-short-info">
				<p>
					 <?php _e('Help potential customers learn about your products and company before even visiting your site. Shopper Approved is a seller rating extension that allows you to collect, manage and promote your customer reviews online for your business. By being a Shopper Approved member, you’ll be able to', 'rich-reviews'); ?>:
				</p>
				<ul>
					<li><strong><?php _e('Team Up', 'rich-reviews'); ?></strong> <?php _e('with a  Google affiliate who has authority and will give your reviews maximum online exposure', 'rich-reviews'); ?>.</li>
					<li><strong><?php _e('Rise', 'rich-reviews'); ?></strong> <?php _e('to the top of SERPs and outrank your competitors', 'rich-reviews'); ?>.</li>
					<li><strong><?php _e('Improve', 'rich-reviews'); ?></strong> <?php _e('brand image through company transparency and honesty', 'rich-reviews'); ?>.</li>
					<li><strong><?php _e('Increase', 'rich-reviews'); ?></strong> <?php _e('Click-Through-Rates', 'rich-reviews'); ?> / <strong><?php _e('Decrease', 'rich-reviews'); ?></strong> <?php _e('Cost-Per-Clicks and Bounce rates', 'rich-reviews'); ?>.</li>
				</ul>
			</div>

			<a class="sa-more button active" href="#"><?php _e('Read More', 'rich-reviews'); ?></a>
			<script>
				jQuery(function() {
					jQuery('.sa-more').click(function(e) {
						e.preventDefault();
						jQuery('.sa-long-info').addClass('active');
						jQuery(e.target).remove();
						target = jQuery('.sa-long-info').offset().top - 300;
						jQuery('html, body').animate({scrollTop: target}, 600);
					});
				});
			</script>


			<div class="sa-long-info">
				<p>
					<?php _e('Increased CTR? Smaller bounce rates? Customer reviews visible? If you’re mumbling a “yes please!” to yourself, it may be the right time to become a Shopper Approved member. Having access to your Shopper Approved account through the Rich Reviews plugin will create ease in managing both accounts and also accelerate fulfilling your marketing KPIs.', 'rich-reviews'); ?>
				</p>

				<h3><?php _e('What is Shopper Approved?', 'rich-reviews'); ?></h3>
				<p>
					<?php _e('Shopper Approved is a seller rating extension that allows you to collect, manage and promote your customer reviews online for your business. Showcasing reviews creates an overall positive image for companies to current and potential customers. Since Shopper Approved is a certified Google partner, your reviews will get maximum exposure in the online world. Read more about the importance of Shopper Approved', 'rich-reviews'); ?> <a href="http://nuancedmedia.com/shopper-approved-review/"><?php _e('here', 'rich-reviews'); ?></a>.
				</p>

				<h3><?php _e('Why choose Shopper Approved?', 'rich-reviews'); ?></h3>
				<p>
					<?php _e('Below is a list of benefits and features of Shopper Approved.', 'rich-reviews'); ?>
					<ul>
						<li><?php _e('Collects 70% more reviews and are visible on more search engines than their competitors.', 'rich-reviews'); ?></li>
						<li><?php _e('Provides clients the ability to improve customer engagement.', 'rich-reviews'); ?></li>
						<li><?php _e('Makes customer reviews visible to public on SERPs - 88% of consumers trust online reviews as much as personal recommendations.', 'rich-reviews'); ?></li>
						<li><?php _e('Increased CTR - Google found that there was an average of a 17% rise in CTR for advertisers with seller rating extensions.', 'rich-reviews'); ?></li>
						<li><?php _e('Companies can improve their brand image by displaying customer reviews on product, merchant and local listings.', 'rich-reviews'); ?></li>
						<li><?php _e('Increased quality score of your PPC.', 'rich-reviews'); ?></li>
						<li><?php _e('Reduction of CPC.', 'rich-reviews'); ?></li>
						<li><?php _e('Increased conversion rates', 'rich-reviews'); ?></li>
						<li><?php _e('Shows company transparency, which gains trust from customers', 'rich-reviews'); ?>.</li>
						<li><?php _e('Easy to set up - learn how to set up your account here (link Gabe’s blog).', 'rich-reviews'); ?></li>
						<li><?php _e('There are tools to create surveys, special offers, and promotional emails.', 'rich-reviews'); ?></li>
						<li><?php _e('Offers a full and free 30-day trial with no strings attached.', 'rich-reviews'); ?></li>
						<li><?php _e('Has less expensive monthly subscription compared to some competitors.', 'rich-reviews'); ?></li>
					</ul>
				</p>

				<h3><?php _e('Try it out!', 'rich-reviews'); ?></h3>
				<p>
					<?php _e('It’s human nature to research and test things out before committing, especially when it’s dealing with important information such as customer opinions about your company and/or products. Doing a trial run takes a few easy steps', 'rich-reviews'); ?>:
					<ol>
						<li><?php _e('Sign up - Take advantage of the free 30-day trial by signing up under our Nuanced Media', 'rich-reviews'); ?> <a href="http://shopperapproved.nuancedmedia.com/?__hssc=113690392.4.1454003621287&__hstc=113690392.f77e147f4173519da9419b7de9791d0a.1448152135690.1453936006034.1454003621287.10&__hsfp=&hsCtaTracking=8e404912-43d5-4ec0-bef0-7d0b038885d1%7C9af74388-d961-427d-9105-1af25a0ef8e1"><?php _e('discount code', 'rich-reviews'); ?></a>, <?php _e('which will give you an extra 20% off a membership if you choose to continue.', 'rich-reviews'); ?></li>
						<li><?php _e('Learn how to easily set up your account through our tutorial (link to Gabe’s blog).', 'rich-reviews'); ?></li>
						<li><?php _e('Explore and test Shopper Approved for 30 days.', 'rich-reviews'); ?></li>
					</ol>
				</p>

				<h3><?php _e('More Information', 'rich-reviews'); ?></h3>
				<p>
					<?php _e('For more information or help with Shopper Approved, read our company’s ', 'rich-reviews'); ?><a href="http://nuancedmedia.com/tag/shopper-approved/"><?php _e('thoughts and experiences', 'rich-reviews'); ?></a> <?php _e('with Shopper Approved or visit the', 'rich-reviews'); ?> <a href="http://www.shopperapproved.com/"><?php _e('Shopper Approved website', 'rich-reviews'); ?></a>.
				</p>
			</div>

			<?php } ?>
					</div>



						<style>
							.postbox-container {
								float: none;
							}
						</style>

