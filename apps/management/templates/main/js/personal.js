$import(function(){
	if (!jQuery().uniform) {
		return;
	}
	var test = $("#user_personal_info input[name='gender']:not(.toggle, .star, .make-switch)");
	if (test.size() > 0) {
		test.each(function () {
			if ($(this).parents(".checker").size() == 0) {
				$(this).show();
				$(this).uniform();
			}
		});
	}

});