<h3><strong><?php _e('User Options', 'rich-reviews'); ?></strong></h3>
<div style="border: solid 2px black"></div>
<input type="checkbox" name="integrate-user-info" value="checked" id="userOptionToggle" <?php echo $options['integrate-user-info'] ?> />
<label for="integrate-user-info">
	<h4 style="display:inline"><?php _e('Integrate User Accounts', 'rich-reviews'); ?></h4>
</label>
<br />
<div id="userOptionsMain" style="padding:8px;">
	<input type="checkbox" name="form-name-use-usernames" value="checked" <?php echo $options['form-name-use-usernames'] ?> />
	<label for="form-name-use-usernames">
		<?php _e('Autofill name from User Accounts.', 'rich-reviews'); ?>
	</label>
	<br />
	<input type="checkbox" name="form-name-use-avatar" value="checked" id="useAvatarToggle" <?php echo $options['form-name-use-avatar'] ?> />
	<label for="form-name-use-avatar">
		<?php _e('Use user avatars.', 'rich-reviews'); ?>
	</label>
	<br />
	<input type="checkbox" name="require-login" value="checked" id="loginGate"<?php echo $options['require-login'] ?> />
	<label for="require-login">
		<?php _e('Require Users to be logged in to submit reviews.', 'rich-reviews'); ?>
	</label>
	<div id="userOptionsUnregisteredSub">
		<input type="checkbox" name="unregistered-allow-avatar-upload" value="checked" <?php echo $options['unregistered-allow-avatar-upload'] ?> />
		<label for="unregistered-allow-avatar-upload">
			<?php _e('Allow image upload for non-logged in reviewers, to be used in place of user avatar in review display.', 'rich-reviews'); ?>
		</label>
	</div>
	<br />
</div>

<script type="text/javascript" >

	jQuery(function() {
		checkParentCondition('input[id="userOptionToggle"]:checked', '#userOptionsMain');
		checkParentCondition('input[id="loginGate"]:checked', '#userOptionsUnregisteredSub', true);
		jQuery('#userOptionToggle').click(function() {
			checkParentCondition('input[id="userOptionToggle"]:checked', '#userOptionsMain');
		});
		jQuery('#loginGate').click(function(){
			checkParentCondition('input[id="loginGate"]:checked', '#userOptionsUnregisteredSub', true);
		});
	});

	checkParentCondition = function(parentSelector, childSelector, reverse) {
		if(typeof(reverse) == undefined) {
			reverse = false;
		}
		childSelector = '' + childSelector +  '';
		parentSelector = '' + parentSelector +  '';
		if(reverse == true) {
			if(jQuery(parentSelector).length == 0) {
				jQuery(childSelector).css('display', 'block');
			} else {
				jQuery(childSelector).css('display', 'none');
			}
		} else {
			if(jQuery(parentSelector).length > 0) {
				jQuery(childSelector).css('display', 'block');
			} else {
				jQuery(childSelector).css('display', 'none');
			}
		}
	}
</script>

<?php dump($options['integrate-user-info']); ?>
<?php dump($options['form-name-use-usernames']); ?>
<?php dump($options['form-name-use-avatar']); ?>
<?php dump($options['require-login']); ?>
<?php dump($options['unregistered-allow-avatar-upload']); ?>
