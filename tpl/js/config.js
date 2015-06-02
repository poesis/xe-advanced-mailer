
(function($) {
	$(function() {
		$("#advanced_mailer_send_type").on("change", function() {
			var send_type = $(this).val();
			$("div.x_control-group").each(function() {
				var visible_types = $(this).data("visible-types");
				if (!visible_types) return;
				visible_types = visible_types.split(" ");
				if ($.inArray(send_type, visible_types) > -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}).trigger("change");
		$("#advanced_mailer_smtp_manual_entry").on("change", function() {
			var auto_fill = $(this).val();
			if (auto_fill === 'gmail') {
				$("#advanced_mailer_smtp_host").val('smtp.gmail.com');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
			}
			if (auto_fill === 'hanmail') {
				$("#advanced_mailer_smtp_host").val('smtp.daum.net');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
			}
			if (auto_fill === 'naver') {
				$("#advanced_mailer_smtp_host").val('smtp.naver.com');
				$("#advanced_mailer_smtp_port").val('587');
				$("#advanced_mailer_smtp_security_tls").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_ssl").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
			}
			if (auto_fill === 'naver_works') {
				$("#advanced_mailer_smtp_host").val('smtp.works.naver.com');
				$("#advanced_mailer_smtp_port").val('587');
				$("#advanced_mailer_smtp_security_tls").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_ssl").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
			}
			if (auto_fill === 'outlook') {
				$("#advanced_mailer_smtp_host").val('smtp-mail.outlook.com');
				$("#advanced_mailer_smtp_port").val('587');
				$("#advanced_mailer_smtp_security_tls").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_ssl").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
			}
			if (auto_fill === 'yahoo') {
				$("#advanced_mailer_smtp_host").val('smtp.mail.yahoo.com');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
			}
		});
	});
} (jQuery));
