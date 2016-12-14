
(function($) {
	
	$(function() {
		
		$("#advanced_mailer_is_enabled").on("change", function() {
			if ($(this).val() === 'Y') {
				$("#advanced_mailer section").not(".show-when-disabled").show();
				$("#advanced_mailer section.show-when-disabled div.x_control-group").not(".show-when-disabled").show();
			} else {
				$("#advanced_mailer section").not(".show-when-disabled").hide();
				$("#advanced_mailer section.show-when-disabled div.x_control-group").not(".show-when-disabled").hide();
			}
		}).triggerHandler("change");
		
		$("#advanced_mailer_sending_method").on("change", function() {
			var sending_method = $(this).val();
			$("div.x_control-group.hidden-by-default").not(".show-always").each(function() {
				if ($(this).hasClass("show-for-" + sending_method)) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			var reply_to = $("#advanced_mailer_reply_to").parents("div.x_control-group");
			if (sending_method === "woorimail") {
				reply_to.hide();
			} else {
				reply_to.show();
			}
		}).triggerHandler("change");
		
		$("#advanced_mailer_smtp_manual_entry").on("change", function() {
			var auto_fill = $(this).val();
			if (auto_fill === 'gmail') {
				$("#advanced_mailer_smtp_host").val('smtp.gmail.com');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
				$("#advanced_mailer_force_sender").prop("checked", true).parent().addClass("checked");
			}
			if (auto_fill === 'hanmail') {
				$("#advanced_mailer_smtp_host").val('smtp.daum.net');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
				$("#advanced_mailer_force_sender").prop("checked", true).parent().addClass("checked");
			}
			if (auto_fill === 'naver') {
				$("#advanced_mailer_smtp_host").val('smtp.naver.com');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
				$("#advanced_mailer_force_sender").prop("checked", true).parent().addClass("checked");
			}
			if (auto_fill === 'worksmobile') {
				$("#advanced_mailer_smtp_host").val('smtp.worksmobile.com');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
				$("#advanced_mailer_force_sender").prop("checked", true).parent().addClass("checked");
			}
			if (auto_fill === 'outlook') {
				$("#advanced_mailer_smtp_host").val('smtp-mail.outlook.com');
				$("#advanced_mailer_smtp_port").val('587');
				$("#advanced_mailer_smtp_security_tls").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_ssl").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
				$("#advanced_mailer_force_sender").prop("checked", true).parent().addClass("checked");
			}
			if (auto_fill === 'yahoo') {
				$("#advanced_mailer_smtp_host").val('smtp.mail.yahoo.com');
				$("#advanced_mailer_smtp_port").val('465');
				$("#advanced_mailer_smtp_security_ssl").prop("checked", true).parent().addClass("checked");
				$("#advanced_mailer_smtp_security_tls").parent().removeClass("checked");
				$("#advanced_mailer_smtp_security_none").parent().removeClass("checked");
				$("#advanced_mailer_force_sender").prop("checked", true).parent().addClass("checked");
			}
		});
		
		$("#advanced_mailer_woorimail_account_type_free,#advanced_mailer_woorimail_account_type_paid").on("change", function() {
			if ($("#advanced_mailer_woorimail_account_type_paid").is(":checked")) {
				$("#advanced_mailer_reply_to").attr("disabled", "disabled");
			} else {
				$("#advanced_mailer_reply_to").removeAttr("disabled");
			}
		}).triggerHandler("change");
		
		$("#advanced_mailer_test_send").click(function(event) {
			event.preventDefault();
			$("#advanced_mailer_test_result").text("");
			$(this).attr("disabled", "disabled");
			var ajax_data = {
				log_sent_mail: $("#advanced_mailer_log_sent_mail").val(),
				log_errors: $("#advanced_mailer_log_errors").val(),
				sending_method: $("#advanced_mailer_sending_method").val(),
				sender_name: $("#advanced_mailer_sender_name").val(),
				sender_email: $("#advanced_mailer_sender_email").val(),
				reply_to: $("#advanced_mailer_reply_to").val(),
				recipient_name: $("#advanced_mailer_recipient_name").val(),
				recipient_email: $("#advanced_mailer_recipient_email").val(),
				use_exceptions: $("#advanced_mailer_use_exceptions").val()
			};
			$.each(advanced_mailer_sending_methods, function(sending_method, sending_conf) {
				$.each(sending_conf.conf, function(key, conf_name) {
					var conf_input = $("#advanced_mailer_" + sending_method + "_" + conf_name);
					if (conf_input.size()) {
						ajax_data[sending_method + "_" + conf_name] = conf_input.val();
					} else {
						ajax_data[sending_method + "_" + conf_name] = $("input[type='radio'][name='" + sending_method + "_" + conf_name + "']:checked").val();
					}
				});
			});
			$.exec_json(
				"advanced_mailer.procAdvanced_mailerAdminTestSend", ajax_data,
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
