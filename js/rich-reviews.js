jQuery(function(){
	jQuery('.rr_review_text').each(function(event){
		var max_length = 150;
		if(jQuery(this).html().length > max_length){
			while(jQuery(this).html().charAt(max_length) != ' ') {
				max_length += 1;
			}
			var short_content 	= jQuery(this).html().substr(0,max_length);
			var long_content	= jQuery(this).html().substr(max_length);
			jQuery(this).html(short_content+'<span class="ellipses">... </span><a href="#" class="read_more"><br />Read More</a>'+'<span class="more_text" style="display:none;">'+long_content+' <br /><a href="#" class="show_less" style="display:none;">Less</a></span>');
			jQuery(this).find('a.read_more').click(function(event){
				event.preventDefault();
				jQuery(this).hide();
				jQuery(this).parents('.rr_review_text').find('span.ellipses').hide();
				jQuery(this).parents('.rr_review_text').find('.more_text').show();
				jQuery(this).parents('.rr_review_text').find('a.show_less').show();

			});
			jQuery(this).find('a.show_less').click(function(event){
				event.preventDefault();
				jQuery(this).hide();
				jQuery(this).parents('.rr_review_text').find('.ellipses').show();
				jQuery(this).parents('.rr_review_text').find('.more_text').hide();
				jQuery(this).parents('.rr_review_text').find('a.read_more').show();
			});
		}
	});

	jQuery('.toggle-shop-app-config').click(function(){
		jQuery('.shop-app-init').toggleClass('active');
		jQuery('.shop-app-info').toggleClass('active');

	});

	initStarEffects();
});

function initStarEffects() {
	jQuery('.rr_review_form').each(function() {

		jQuery(this).find('.rr_star').hover(function() {
			renderStarRating(parseInt(jQuery(this).attr('id').charAt(8)), jQuery(this).parent());
		}, function() {
			renderStarRating(parseInt(jQuery(this).parent().closest('form').find('#rRating').val()), jQuery(this).parent());
		});

		jQuery(this).find('.rr_star').click(function(e) {
			thing = jQuery(this).closest('form').find('#rRating');

			thing.val(jQuery(this).attr('id').charAt(8));
		});
	});
}

function renderStarRating(rating, target) {
	for (var i=1; i<=5; i++) {
		target.find('#rr_star_'+i).removeClass('glyphicon-star');
		target.find('#rr_star_'+i).removeClass('glyphicon-star-empty');
		if (i<=rating) {
			target.closest('form').find('#rr_star_'+i).addClass('glyphicon-star');
		} else {
			target.closest('form').find('#rr_star_'+i).addClass('glyphicon-star-empty');
		}
	}
}

