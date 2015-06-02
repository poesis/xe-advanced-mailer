
(function($) {
	$(function() {
		$("#advanced_mailer_send_type").on("change", function() {
			var send_type = $(this).val();
			$("div.x_control-group").each(function() {
				var visible_types = $(this).data("visible-types");
				if(!visible_types) return;
				visible_types = visible_types.split(" ");
				if($.inArray(send_type, visible_types) > -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}).trigger("change");
	});
} (jQuery));
