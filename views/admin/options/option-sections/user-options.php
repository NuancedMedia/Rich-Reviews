<h3><strong><?php _e('User Options', 'rich-reviews'); ?></strong></h3>
<div style="border: solid 2px black"></div>
<input type="checkbox" name="integrate-user-info" value="checked" id="userOptionToggle" <?php echo $options['integrate-user-info'] ?> />
<label for="integrate-user-info">
	<h4 style="display:inline"><?php _e('Integrate User Accounts', 'rich-reviews'); ?></h4>
</label>
<br />
<div id="userOptionsMain" style="padding:8px;">
<input type="checkbox" name="require-login" value="checked" id="loginGate"<?php echo $options['require-login'] ?> />
	<label for="require-login">
		<?php _e('Require Users to be logged in to submit reviews.', 'rich-reviews'); ?>
	</label>
	<div id="loginRequiredSub" style="padding: 13px;">
		<label for="login-url">
			<?php _e('Login Url for link in Review Gate', 'rich-reviews'); ?>
		</label>
		<br />
		<input type="text" name="login-url" value="<?php if(isset($options['login-url']) && $options['login-url'] != '') { echo $options['login-url']; } else { echo wp_login_url(); } ?>" style="width: 90%;"/>
	</div>
	<br />
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
	<div id="useAvatarSub">
		<input type="checkbox" name="form-name-use-blank-avatars" value="checked" id="useBlankAvatarToggle" <?php echo $options['form-name-use-blank-avatar'] ?> />
		<label for="form-name-use-blank-avatar">
			<?php _e('Use blank avatar for users without uploaded image/gravatar.', 'rich-reviews'); ?>
		</label>
	</div>

	<div id="userOptionsUnregisteredSub">
		<input type="checkbox" name="unregistered-allow-avatar-upload" id="unregisteredUpload" value="checked" <?php echo $options['unregistered-allow-avatar-upload'] ?> />
		<label for="unregistered-allow-avatar-upload">
			<?php _e('Allow image upload for non-logged in reviewers, to be used in place of user avatar in review display.', 'rich-reviews'); ?>
		</label>
		<div id="unregisteredAvatarAllowedSub" style="padding:13px;">
			<h4><?php _e('Reviewer Image Field', 'rich-reviews'); ?></h4>
			<label for="form-reviewer-image-label">Form Label:</label>
			<input type="text" name="form-reviewer-image-label" value="<?php echo $options['form-reviewer-image-label']; ?>" />
			<br />
			<label for="form-reviewer-image-require">Require Field: </label>
			<input type="checkbox" name="form-reviewer-image-require" value="checked" <?php echo $options['form-reviewer-image-require'] ?> />
		</div>
	</div>
	<br />
</div>

<script type="text/javascript" >

	jQuery(function() {
		checkParentCondition('input[id="userOptionToggle"]:checked', '#userOptionsMain');
		checkParentCondition('input[id="loginGate"]:checked', '#userOptionsUnregisteredSub', true);
		checkParentCondition('input[id="loginGate"]:checked', '#loginRequiredSub');
		checkParentCondition('input[id="unregisteredUpload"]:checked', '#unregisteredAvatarAllowedSub');
		checkParentCondition('input[id="useAvatarToggle"]:checked', '#useAvatarSub');
		jQuery('#userOptionToggle').click(function() {
			checkParentCondition('input[id="userOptionToggle"]:checked', '#userOptionsMain');
		});
		jQuery('#loginGate').click(function(){
			checkParentCondition('input[id="loginGate"]:checked', '#loginRequiredSub');
			checkParentCondition('input[id="loginGate"]:checked', '#userOptionsUnregisteredSub', true);
		});
		jQuery('#unregisteredUpload').click(function(){
			checkParentCondition('input[id="unregisteredUpload"]:checked', '#unregisteredAvatarAllowedSub');
		});
		jQuery('#useAvatarToggle').click(function(){
			checkParentCondition('input[id="useAvatarToggle"]:checked', '#useAvatarSub');
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
