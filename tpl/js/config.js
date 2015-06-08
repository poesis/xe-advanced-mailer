
(function($) {
	
	$(function() {
		
		var ignore_domains = {
			"gmail.com" : true,
			"googlemail.com" : true,
			"hanmail.net" : true,
			"hanmail2.net" : true,
			"daum.net" : true,
			"naver.com" : true,
			"hotmail.com" : true,
			"hotmail.co.kr" : true,
			"outlook.com" : true,
			"yahoo.com" : true,
			"yahoo.co.kr" : true
		};
			
		var list_spf_dkim = {
			"mail" : ["", ""],
			"ses" : ["", ""],
			"mailgun" : ["include:mailgun.org", "mailo._domainkey"],
			"mandrill" : ["include:spf.mandrillapp.com", "mandrill._domainkey"],
			"postmark": ["include:spf.mtasv.net", "********.pm._domainkey"],
			"sendgrid" : ["include:sendgrid.net", "smtpapi._domainkey"]
		};
		
		var reset_spf_dkim = function() {
			var div_spf_dkim = $("#spf_dkim_setting");
			div_spf_dkim.find("div.not_applicable").show();
			div_spf_dkim.find("div.config_description,div.config_value,div.config_other").hide();
		};
		
		var update_spf_dkim = function() {
			var send_type = $("#advanced_mailer_send_type").val();
			if (!list_spf_dkim[send_type]) {
				return reset_spf_dkim();
			}
			var sender_email = $("#advanced_mailer_sender_email").val();
			var sender_domain = null;
			if (sender_email.lastIndexOf("@") > -1) {
				sender_domain = sender_email.substr(sender_email.lastIndexOf("@") + 1).toLowerCase();
				if (!sender_domain || ignore_domains[sender_domain]) {
					return reset_spf_dkim();
				}
			} else {
				return reset_spf_dkim();
			}
			var div_spf_dkim = $("#spf_dkim_setting");
			div_spf_dkim.find("div.not_applicable").hide();
			if (list_spf_dkim[send_type][0]) {
				div_spf_dkim.find("div.spf.config_description").show().find("span.hostname").text(sender_domain);
				div_spf_dkim.find("div.spf.config_value").show().find("span.value").text(("v=spf1 a mx " + list_spf_dkim[send_type][0] + " ~all").replace("  ", " "));
			} else {
				div_spf_dkim.find("div.spf.not_applicable").show();
				div_spf_dkim.find("div.spf.config_value,div.spf.config_description,div.spf.config_other").hide();
			}
			if ($("#spf_dkim_setting").data("other-" + send_type + "-spf")) {
				div_spf_dkim.find("div.spf.not_applicable").hide();
				div_spf_dkim.find("div.spf.config_other").show().find("span.other").html($("#spf_dkim_setting").data("other-" + send_type + "-spf"));
			} else {
				div_spf_dkim.find("div.spf.config_other").hide();
			}
			if (list_spf_dkim[send_type][1]) {
				div_spf_dkim.find("div.dkim.config_description").show().find("span.hostname").text(list_spf_dkim[send_type][1] + "." + sender_domain);
				div_spf_dkim.find("div.dkim.config_value").show().find("span.value").text("v=DKIM1; k=rsa; p=MIGfMA ..." + $("#spf_dkim_setting").data("ellipsis") + "... QAB;");
			} else {
				div_spf_dkim.find("div.dkim.not_applicable").show();
				div_spf_dkim.find("div.dkim.config_value,div.dkim.config_description,div.dkim.config_other").hide();
			}
			if ($("#spf_dkim_setting").data("other-" + send_type + "-dkim")) {
				div_spf_dkim.find("div.dkim.not_applicable").hide();
				div_spf_dkim.find("div.dkim.config_other").show().find("span.other").html($("#spf_dkim_setting").data("other-" + send_type + "-dkim"));
			} else {
				div_spf_dkim.find("div.dkim.config_other").hide();
			}
		};
		
		if ($("#spf_dkim_setting").data("server-ip")) {
			list_spf_dkim["mail"][0] = "ip4:" + $("#spf_dkim_setting").data("server-ip");
		}
		
		$("#advanced_mailer_sender_email").on("change keyup blur", update_spf_dkim);
		
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
			update_spf_dkim();
		}).triggerHandler("change");
		
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
		
		$("#advanced_mailer_account_type_free,#advanced_mailer_account_type_paid").on("change", function() {
			if ($("#advanced_mailer_account_type_paid").is(":checked")) {
				$("#advanced_mailer_reply_to").attr("disabled", "disabled");
			} else {
				$("#advanced_mailer_reply_to").removeAttr("disabled");
			}
		}).triggerHandler("change");
		
		$("#advanced_mailer_check_spf,#advanced_mailer_check_dkim").click(function(event) {
			event.preventDefault();
			var check_type = $(this).attr("id").match(/_spf$/) ? "spf" : "dkim";
			var check_hostname = $("#spf_dkim_setting div." + check_type + ".config_description span.hostname").text();
			if (!check_hostname) {
				alert($("#spf_dkim_setting").data("nothing-to-check"));
			}
			$(this).attr("disabled", "disabled");
			$.exec_json(
				"advanced_mailer.procAdvanced_mailerAdminCheckDNSRecord",
				{ hostname: check_hostname, record_type: "TXT" },
				function(response) {
					if (response.record_content === false) {
						alert($("#spf_dkim_setting").data("check-failure"));
					}
					else if (response.record_content === "") {
						alert('<span class="monospace">' + check_hostname + "</span> " +
							$("#spf_dkim_setting").data("check-no-records"));
						$(".x_modal._common._small").removeClass("_small");
					}
					else {
						alert('<span class="monospace">' + check_hostname + "</span> " +
							$("#spf_dkim_setting").data("check-result") + "<br /><br />" +
							'<div class="monospace">' + response.record_content.replace("\n", "<br />") + "</div>");
						$(".x_modal._common._small").removeClass("_small");
					}
					$("#advanced_mailer_check_" + check_type).removeAttr("disabled");
				},
				function(response) {
					alert($("#spf_dkim_setting").data("check-failure"));
					$("#advanced_mailer_check_" + check_type).removeAttr("disabled");
				}
			);
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
				account_type: $("input[type='radio'][name='account_type']:checked").val(),
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
