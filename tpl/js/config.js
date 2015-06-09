
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
		
		var reset_spf_dkim = function() {
			var div_spf_dkim = $("#spf_dkim_setting");
			div_spf_dkim.find("div.not_applicable").show();
			div_spf_dkim.find("div.config_description,div.config_value,div.config_other").hide();
		};
		
		var update_spf_dkim = function() {
			var sending_method = $("#advanced_mailer_sending_method").val();
			if (!advanced_mailer_sending_methods[sending_method]) {
				return reset_spf_dkim();
			}
			if (sending_method === "woorimail" && !($("#advanced_mailer_woorimail_account_type_paid").is(":checked"))) {
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
			if (advanced_mailer_sending_methods[sending_method]["spf"]) {
				div_spf_dkim.find("div.spf.config_description").show().find("span.hostname").text(sender_domain);
				div_spf_dkim.find("div.spf.config_value").show().find("span.value").text(("v=spf1 a mx " + advanced_mailer_sending_methods[sending_method]["spf"] + " ~all").replace("  ", " "));
			} else {
				div_spf_dkim.find("div.spf.not_applicable").show();
				div_spf_dkim.find("div.spf.config_value,div.spf.config_description,div.spf.config_other").hide();
			}
			if ($("#spf_dkim_setting").data("other-" + sending_method + "-spf")) {
				div_spf_dkim.find("div.spf.not_applicable").hide();
				div_spf_dkim.find("div.spf.config_other").show().find("span.other").html($("#spf_dkim_setting").data("other-" + sending_method + "-spf"));
			} else {
				div_spf_dkim.find("div.spf.config_other").hide();
			}
			if (advanced_mailer_sending_methods[sending_method]["dkim"]) {
				div_spf_dkim.find("div.dkim.config_description").show().find("span.hostname").text(advanced_mailer_sending_methods[sending_method]["dkim"] + "." + sender_domain);
				div_spf_dkim.find("div.dkim.config_value").show().find("span.value").text("v=DKIM1; k=rsa; p=MIGfMA ..." + $("#spf_dkim_setting").data("ellipsis") + "... QAB;");
			} else {
				div_spf_dkim.find("div.dkim.not_applicable").show();
				div_spf_dkim.find("div.dkim.config_value,div.dkim.config_description,div.dkim.config_other").hide();
			}
			if ($("#spf_dkim_setting").data("other-" + sending_method + "-dkim")) {
				div_spf_dkim.find("div.dkim.not_applicable").hide();
				div_spf_dkim.find("div.dkim.config_other").show().find("span.other").html($("#spf_dkim_setting").data("other-" + sending_method + "-dkim"));
			} else {
				div_spf_dkim.find("div.dkim.config_other").hide();
			}
		};
		
		if ($("#spf_dkim_setting").data("server-ip")) {
			advanced_mailer_sending_methods["mail"]["spf"] = "ip4:" + $("#spf_dkim_setting").data("server-ip");
		}
		
		$("#advanced_mailer_sender_email").on("change keyup blur", update_spf_dkim);
		
		$("#advanced_mailer_sending_method").on("change", function() {
			var sending_method = $(this).val();
			$("div.x_control-group.hidden-by-default").not(".show-always").each(function() {
				if ($(this).hasClass("show-for-" + sending_method)) {
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
		
		$("#advanced_mailer_woorimail_account_type_free,#advanced_mailer_woorimail_account_type_paid").on("change", function() {
			if ($("#advanced_mailer_woorimail_account_type_paid").is(":checked")) {
				$("#advanced_mailer_reply_to").attr("disabled", "disabled");
			} else {
				$("#advanced_mailer_reply_to").removeAttr("disabled");
			}
			update_spf_dkim();
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
			var ajax_data = {
				sending_method: $("#advanced_mailer_sending_method").val(),
				sender_name: $("#advanced_mailer_sender_name").val(),
				sender_email: $("#advanced_mailer_sender_email").val(),
				reply_to: $("#advanced_mailer_reply_to").val(),
				recipient_name: $("#advanced_mailer_recipient_name").val(),
				recipient_email: $("#advanced_mailer_recipient_email").val()
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
