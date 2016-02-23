<?php

if (isset($_GET['id']) && $_GET['id'] != '') {
	if (isset($options['product_catalog_ids']) && !empty($options['product_catalog_ids'])) {
		if (isset($options['product_catalog_ids'][$_GET['id']])) {
			$data = $options['product_catalog_ids'][$_GET['id']];

			$name = '';
			$description = '';
			$manufacturer = '';
			$product_url = '';
			$image_url = '';
			$mpn = '';

			extract($data);

			?>
			<div class="edit-page-container">
				<a href="<?php echo admin_url() . 'admin.php?page=fp_admin_shopper_approved_page'; ?>" class="button">
					<?php echo '< ' . __('Back to Options', 'rich-reviews'); ?>
				</a>
				<h1>Edit Single Product Catalog Listing</h1>

				<?php
					if (isset($message_text) && $message_text != '') {
						?>
							<div id="message" class="<?php echo $message_class; ?> notice notice-success is-dismissible below-h2"><p>
								<?php echo $message_text; ?>
							</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div><br/>
						<?php
					}
				?>
				<div class="index-edit-container">
					<form name="single-product-index" id="edit-index-<?php echo $_GET['id']; ?>" method="post">
						<input type="hidden" name="toBeOrNotToBe" value="theQuestion" />
						<fieldset for="id">
							<div class="index-field-label">
								<label for="id">
									<?php _e('ID', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<input type="text" name="id" value='<?php echo $_GET['id']; ?>' disabled/>
							</div>
						</fieldset>
						<fieldset for="name">
							<div class="index-field-label">
								<label for="name">
									<?php _e('Name', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<input type="text" name="name" value='<?php echo stripcslashes($name); ?>'/>
							</div>
						</fieldset>
						<fieldset for="description">
							<div class="index-field-label">
								<label for="description">
									<?php _e('Description', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<textarea type="text" name="description"><?php echo stripcslashes($description); ?></textarea>
							</div>
						</fieldset>
						<fieldset for="manufacturer">
							<div class="index-field-label">
								<label for="manufacturer">
									<?php _e('Manufacturer', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<input type="text" name="manufacturer" value='<?php echo stripcslashes($manufacturer); ?>'/>
							</div>
						</fieldset>
						<fieldset for="product_url">
							<div class="index-field-label">
								<label for="product_url">
									<?php _e('Product Url', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<input type="text" name="product_url" value='<?php echo stripcslashes($product_url); ?>'/>
							</div>
						</fieldset>
						<fieldset for="image_url">
							<div class="index-field-label">
								<label for="image_url">
									<?php _e('Image Url', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<input type="text" name="image_url" value='<?php echo stripcslashes($image_url); ?>'/>
							</div>
						</fieldset>
						<fieldset for="mpn">
							<div class="index-field-label">
								<label for="mpn">
									<?php _e('MPN (or alternate ID)', 'rich-reviews'); ?>:
								</label>
							</div>
							<div class="index-field-input">
								<input type="text" name="mpn" value='<?php echo stripcslashes($mpn); ?>'/>
							</div>
						</fieldset>
						<input type="submit" class="button left" value="Update Product Listing"/>
						<button class="button delete-listing-button" id="delete-listing"><?php _e('Delete Product Listing', 'rich-reviews'); ?></button>
						<div class="clear"></div>
					</form>
				</div>
				<div class="confirm-deletion">
					<h3><?php printf(__('Are you sure that you wish to delete the product listing for product with id "%s"?', 'rich-reviews'), $_GET['id']); ?></h3>
					<form method="post" name="delete-listing-<?php echo $_GET['id'];?>">
						<input type="hidden" name="deleting-listing" value="confirmed" />
						<input type="submit" class="button confirm-deletion-button" value="<?php _e('Confirm Delete', 'rich-reviews'); ?>" />
						<button class="button delete-listing-button" id="cancel-deletion"><?php _e('Cancel Delete'); ?></button>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(function() {
					jQuery('#delete-listing').click(function(e) {
						e.preventDefault();
						jQuery('.confirm-deletion').toggleClass('active');
					});
					jQuery('#cancel-deletion').click(function(e) {
						e.preventDefault();
						jQuery('.confirm-deletion').removeClass('active');
					});
				});
			</script>
			<?php
		} else {
			?>
				<div class="edit-page-container">
					<a href="<?php echo admin_url() . 'admin.php?page=fp_admin_shopper_approved_page'; ?>" class="button">
						<?php echo '< ' . __('Back to Options', 'rich-reviews'); ?>
					</a>
					<h1>Product Listing Not Found</h1>
				</div>
			<?php
		}
	}
} else if (isset($_GET['new']) && $_GET['new'] == 'true') {

	?>
		<div class="edit-page-container">
			<a href="<?php echo admin_url() . 'admin.php?page=fp_admin_shopper_approved_page'; ?>" class="button">
				<?php echo '< ' . __('Back to Options', 'rich-reviews'); ?>
			</a>
			<h1><?php _e('Add Single Product Catalog Listing', 'rich-reviews'); ?></h1>

			<?php
				if (isset($message_text) && $message_text != '') {
					?>
						<div id="message" class="<?php echo $message_class; ?> notice notice-success is-dismissible below-h2"><p>
							<?php echo $message_text; ?>
						</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div><br/>
					<?php
				}
			?>
			<div class="index-edit-container">
				<form name="single-product-index" id="new-index" method="post">
					<input type="hidden" name="toBeOrNotToBe" value="theQuestionaire" />
					<fieldset for="id">
						<div class="index-field-label">
							<label for="id">
								<?php _e('ID', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<input type="text" name="id" value=''/>
						</div>
					</fieldset>
					<fieldset for="name">
						<div class="index-field-label">
							<label for="name">
								<?php _e('Name', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<input type="text" name="name" value=''/>
						</div>
					</fieldset>
					<fieldset for="description">
						<div class="index-field-label">
							<label for="description">
								<?php _e('Description', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<textarea type="text" name="description"></textarea>
						</div>
					</fieldset>
					<fieldset for="manufacturer">
						<div class="index-field-label">
							<label for="manufacturer">
								<?php _e('Manufacturer', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<input type="text" name="manufacturer" value=''/>
						</div>
					</fieldset>
					<fieldset for="product_url">
						<div class="index-field-label">
							<label for="product_url">
								<?php _e('Product Url', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<input type="text" name="product_url" value=''/>
						</div>
					</fieldset>
					<fieldset for="image_url">
						<div class="index-field-label">
							<label for="image_url">
								<?php _e('Image Url', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<input type="text" name="image_url" value=''/>
						</div>
					</fieldset>
					<fieldset for="mpn">
						<div class="index-field-label">
							<label for="mpn">
								<?php _e('MPN (or alternate ID)', 'rich-reviews'); ?>:
							</label>
						</div>
						<div class="index-field-input">
							<input type="text" name="mpn" value=''/>
						</div>
					</fieldset>
					<input type="submit" class="button left" value="Add Product Listing"/>
					<div class="clear"></div>
				</form>
			</div>
		</div>
	<?php

} else {
	header('HTTP/1.0 404 Not Found');
}
