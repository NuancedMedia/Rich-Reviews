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
				<h3>Already registered with Shopper Approved?</h3>
				<button class="button toggle-shop-app-config">
					Configure Shopper Approved Extension
				</button>
			</div>
			<div class="shop-app-init <?php echo $init_active; ?> options-section">
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
						</form>
					</div>

					<div class="clear"></div>

					<div class="options-section">
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
							<input type="submit" form="shopAppAdmin" class="button" id="force-update" value="Manual Update"/>
						</div>
						<div class="clear"></div>
					</div>

					<div class="options-section">
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
					</div>
					<div class="options-section">
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
						<div class="clear"></div>
					</div>

			<?php	} else { ?>
				</form>
			</div>

			<div class="clear"></div>
			<h2>Shopper Approved</h2>

			<div class="sa-short-info">
				<p>
					 Help potential customers learn about your products and company before even visiting your site. Shopper Approved is a seller rating extension that allows you to collect, manage and promote your customer reviews online for your business. By being a Shopper Approved member, you’ll be able to:
				</p>
				<ul>
					<li><strong>Team Up</strong> with a  Google affiliate who has authority and will give your reviews maximum online exposure.</li>
					<li><strong>Rise</strong> to the top of SERPs and outrank your competitors.</li>
					<li><strong>Improve</strong> brand image through company transparency and honesty.</li>
					<li><strong>Increase</strong> Click-Through-Rates / <strong>Decrease</strong> Cost-Per-Clicks and Bounce rates.</li>
				</ul>
			</div>

			<a class="sa-more button active" href="#">Read More</a>
			<script>
				jQuery(function() {
					jQuery('.sa-more').click(function(e) {
						e.preventDefault();
						jQuery('.sa-long-info').addClass('active');
						jQuery(e.target).remove();
						target = jQuery('.sa-long-info').offset().top - 50;
						jQuery('html, body').animate({scrollTop: target}, 400);
					});
				});
			</script>


			<div class="sa-long-info">
				<p>
					Increased CTR? Smaller bounce rates? Customer reviews visible? If you’re mumbling a “yes please!” to yourself, it may be the right time to become a Shopper Approved member. Having access to your Shopper Approved account through the Rich Reviews plugin will create ease in managing both accounts and also accelerate fulfilling your marketing KPIs.
				</p>

				<h3>What is Shopper Approved?</h3>
				<p>
					Shopper Approved is a seller rating extension that allows you to collect, manage and promote your customer reviews online for your business. Showcasing reviews creates an overall positive image for companies to current and potential customers. Since Shopper Approved is a certified Google partner, your reviews will get maximum exposure in the online world. Read more about the importance of Shopper Approved <a href="http://nuancedmedia.com/shopper-approved-review/">here</a>.
				</p>

				<h3>Why choose Shopper Approved?</h3>
				<p>
					Below is a list of benefits and features of Shopper Approved.
					<ul>
						<li>Collects 70% more reviews and are visible on more search engines than their competitors.</li>
						<li>Provides clients the ability to improve customer engagement.</li>
						<li>Makes customer reviews visible to public on SERPs - 88% of consumers trust online reviews as much as personal recommendations.</li>
						<li>Increased CTR - Google found that there was an average of a 17% rise in CTR for advertisers with seller rating extensions.</li>
						<li>Companies can improve their brand image by displaying customer reviews on product, merchant and local listings.</li>
						<li>Increased quality score of your PPC.</li>
						<li>Reduction of CPC.</li>
						<li>Increased conversion rates</li>
						<li>Shows company transparency, which gains trust from customers.</li>
						<li>Easy to set up - learn how to set up your account here (link Gabe’s blog).</li>
						<li>There are tools to create surveys, special offers, and promotional emails.</li>
						<li>Offers a full and free 30-day trial with no strings attached. </li>
						<li>Has less expensive monthly subscription compared to some competitors.</li>
					</ul>
				</p>

				<h3>Try it out!</h3>
				<p>
					It’s human nature to research and test things out before committing, especially when it’s dealing with important information such as customer opinions about your company and/or products. Doing a trial run takes a few easy steps:
					<ol>
						<li>Sign up - Take advantage of the free 30-day trial by signing up under our Nuanced Media <a href="http://shopperapproved.nuancedmedia.com/?__hssc=113690392.4.1454003621287&__hstc=113690392.f77e147f4173519da9419b7de9791d0a.1448152135690.1453936006034.1454003621287.10&__hsfp=&hsCtaTracking=8e404912-43d5-4ec0-bef0-7d0b038885d1%7C9af74388-d961-427d-9105-1af25a0ef8e1">discount code</a>, which will give you an extra 20% off a membership if you choose to continue.</li>
						<li>Learn how to easily set up your account through our tutorial (link to Gabe’s blog).</li>
						<li>Explore and test Shopper Approved for 30 days.</li>
					</ol>
				</p>

				<h3>More Information</h3>
				<p>
					For more information or help with Shopper Approved, read our company’s <a href="http://nuancedmedia.com/tag/shopper-approved/">thoughts and experiences</a> with Shopper Approved or visit the <a href="http://www.shopperapproved.com/">Shopper Approved website</a>.
				</p>
			</div>

			<?php } ?>
					</div>



						<style>
							.postbox-container {
								float: none;
							}
						</style>

