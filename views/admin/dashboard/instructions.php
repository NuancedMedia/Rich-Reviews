<?php
?>
<div class="wrap">
			</div>


			<div class="rr_admin_sidebar">
				<div class="rr_admin_sidebar_title">
					<?php _e('Shortcode Cheat Sheet', 'rich-reviews'); ?>
				</div>
				<div class="rr_admin_sidebar_text">
					<?php _e('Make sure you read the detailed descriptions of how these work below, in ', 'rich-reviews'); ?> <span style="font-weight: 600;"><?php _e('Shortcode Usage', 'rich-reviews'); ?></span>!
				</div>
				<ul class="rr_admin_sidebar_list">
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SHOW]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SHOW num="9"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SHOW num="all"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SHOW category="foo"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SHOW category="all"]
					</li>
					<li class="rr_admin_sidebar_list_item" style="margin: 0px 0px 4px 	0px;">
						[RICH_REVIEWS_SHOW category="page" id="283" num="5"]
					</li>
					<li class="rr_admin_sidebar_list_item" style="margin: 0px 0px 4px 	0px;">
						[RICH_REVIEWS_SHOW category="all" num="all"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_FORM]
					</li>
					<li class="rr_admin_sidebar_list_item" style="margin: 0px 0px 4px 	0px;">
						[RICH_REVIEWS_FORM category="foo"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SNIPPET]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SNIPPET category="foo"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SNIPPET category="page"]
					</li>
					<li class="rr_admin_sidebar_list_item">
						[RICH_REVIEWS_SNIPPET category="all"]
					</li>
				</ul>
			</div>
			<p><?php printf(__('Thank you for using Rich Reviews by %s and ', 'rich-reviews'), 'Foxy Technologies'); ?> <a href="http://nuancedmedia.com" target="_blank">Nuanced Media</a>!
			</p>
			<p>
			<?php _e('This plugin, based around shortcodes, gives YOU the control on where you want to show your reviews and ratings - pages, posts, widgets... wherever! Don\'t be shy to showcase the results of all your hard work.', 'rich-reviews');
			?> </p>
			 <p><?php
			_e('Some terminology so that we are all on the same page:', 'rich-reviews');?>
				<ul>
					<li><?php _e('A <b>global review</b> is a review which applies or belongs to the entire Wordpress site, regardless of the current page or post. You might use global reviews if your users are submitting reviews for a business or entire website.', 'rich-reviews'); ?>
					</li>
					<li><?php _e('A <b>per-page review</b> is a review which applies to some specific page or post. You might use per-page reviews if, for example, your Wordpress site has various products with a dedicated page or post for each one. Note that reviews users submit will <i>always</i> record the post from which they were submitted, even if you will end up using global reviews! This is to simplify things, so that we don\'t have a bunch of different, confusing shortcodes.', 'rich-reviews'); ?>
					</li>
				</ul>
			</p>
			<p>
			<?php echo __('When you get a chance, please take a moment to', 'rich-reviews') . ' <a href="http://wordpress.org/extend/plugins/rich-reviews/" target="_blank" >rate and/or review</a> ' . __('this plugin. We love hearing the feedback - whether you love it or how we can improve.', 'rich-reviews');
			 ?> </p>
			<div class="clear"></div>
