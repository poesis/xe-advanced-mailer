
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
		
		$("#advanced_mailer_test_send").click(function(event) {
			event.preventDefault();
			$("#advanced_mailer_test_result").text("");
			$(this).attr("disabled", "disabled");
			var data = {
				send_type: $("#advanced_mailer_send_type").val(),
				smtp_host: $("#advanced_mailer_smtp_host").val(),
				smtp_port: $("#advanced_mailer_smtp_port").val(),
				smtp_security: $("input[type='radio'][name='smtp_security']:checked").val(),
				username: $("#advanced_mailer_username").val(),
				password: $("#advanced_mailer_password").val(),
				domain: $("#advanced_mailer_domain").val(),
				api_key: $("#advanced_mailer_api_key").val(),
				aws_region: $("#advanced_mailer_aws_region").val(),
				aws_access_key: $("#advanced_mailer_aws_access_key").val(),
				aws_secret_key: $("#advanced_mailer_aws_secret_key").val(),
				sender_name: $("#advanced_mailer_sender_name").val(),
				sender_email: $("#advanced_mailer_sender_email").val(),
				reply_to: $("#advanced_mailer_reply_to").val(),
				recipient_name: $("#advanced_mailer_recipient_name").val(),
				recipient_email: $("#advanced_mailer_recipient_email").val()
			};
			$.exec_json(
				"advanced_mailer.procAdvanced_mailerAdminTestSend", data,
				function(response) {
					$("#advanced_mailer_test_result").html(response.test_result);
					$("#advanced_mailer_test_send").removeAttr("disabled");
				},
				function(response) {
					$("#advanced_mailer_test_result").text("AJAX Error");
					$("#advanced_mailer_test_send").removeAttr("disabled");
				}
			);
		});
		
	});
	
} (jQuery));
