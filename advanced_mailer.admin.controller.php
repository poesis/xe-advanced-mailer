<?php

/**
 * @file advanced_mailer.admin.controller.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Admin Controller
 */
class Advanced_MailerAdminController extends Advanced_Mailer
{
	/**
	 * Save the basic configuration.
	 */
	public function procAdvanced_MailerAdminInsertConfig()
	{
		// Get and validate the new configuration.
		$config = $this->getRequestVars();
		$validation = $this->validateConfiguration($config);
		if ($validation !== true)
		{
			return $this->createObject(-1, $validation);
		}
		
		// Update the webmaster's name and email in the member module.
		$args = (object)array(
			'webmaster_name' => $config->sender_name,
			'webmaster_email' => $config->sender_email,
		);
		$oModuleController = getController('module');
		$output = $oModuleController->updateModuleConfig('member', $args);
		
		// Save the new configuration.
		$output = getController('module')->insertModuleConfig('advanced_mailer', $config);
		if ($output->toBool())
		{
			$this->setMessage('success_registed');
		}
		else
		{
			return $output;
		}
		
		if (Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAdvanced_mailerAdminConfig'));
		}
	}
	
	/**
	 * Save the exception configuration.
	 */
	public function procAdvanced_MailerAdminInsertExceptions()
	{
		// Get the current configuration.
		$config = $this->getConfig();
		
		// Get and validate the list of exceptions.
		$exceptions = array();
		for ($i = 1; $i <= 3; $i++)
		{
			$method = strval(Context::get('exception_' . $i . '_method'));
			$domains = trim(Context::get('exception_' . $i . '_domains'));
			if ($method !== '' && $domains !== '')
			{
				if ($method !== 'default' && !isset($this->sending_methods[$method]))
				{
					return $this->createObject(-1, 'msg_advanced_mailer_sending_method_is_invalid');
				}
				if ($method !== 'default')
				{
					foreach ($this->sending_methods[$method]['conf'] as $conf_name)
					{
						if (!isset($config->{$method . '_' . $conf_name}) || strval($config->{$method . '_' . $conf_name}) === '')
						{
							return $this->createObject(-1, sprintf(
								Context::getLang('msg_advanced_mailer_sending_method_is_not_configured'),
								Context::getLang('cmd_advanced_mailer_sending_method_' . $method)));
						}
					}
				}
				$exceptions[$i]['method'] = $method;
				$exceptions[$i]['domains'] = array();
				
				$domains = array_map('trim', preg_split('/[,\n]/', $domains, null, PREG_SPLIT_NO_EMPTY));
				foreach ($domains as $domain)
				{
					if (strpos($domain, 'xn--') !== false) $domain = idn_to_utf8($domain);
					$exceptions[$i]['domains'][] = $domain;
				}
			}
		}
		
		// Save the new configuration.
		$config->exceptions = $exceptions;
		$output = getController('module')->insertModuleConfig('advanced_mailer', $config);
		if ($output->toBool())
		{
			$this->setMessage('success_registed');
		}
		else
		{
			return $output;
		}
		
		if (Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAdvanced_mailerAdminExceptions'));
		}
	}
	
	/**
	 * Check the DNS record of a domain.
	 */
	public function procAdvanced_MailerAdminCheckDNSRecord()
	{
		$check_config = Context::gets('hostname', 'record_type');
		if (!preg_match('/^[a-z0-9_.-]+$/', $check_config->hostname))
		{
			$this->add('record_content', false);
			return;
		}
		if (!defined('DNS_' . $check_config->record_type))
		{
			$this->add('record_content', false);
			return;
		}
		
		$records = @dns_get_record($check_config->hostname, constant('DNS_' . $check_config->record_type));
		if ($records === false)
		{
			$this->add('record_content', false);
			return;
		}
		
		$return_values = array();
		foreach ($records as $record)
		{
			if (isset($record[strtolower($check_config->record_type)]))
			{
				$return_values[] = $record[strtolower($check_config->record_type)];
			}
		}
		$this->add('record_content', implode("\n\n", $return_values));
		return;
	}
	
	/**
	 * Clear old sending log.
	 */
	public function procAdvanced_mailerAdminClearSentMail()
	{
		$status = Context::get('status');
		$clear_before_days = intval(Context::get('clear_before_days'));
		if (!in_array($status, array('success', 'error')))
		{
			return $this->createObject(-1, 'msg_invalid_request');
		}
		if ($clear_before_days < 0)
		{
			return $this->createObject(-1, 'msg_invalid_request');
		}
		
		$obj = new stdClass();
		$obj->status = $status;
		$obj->regdate = date('YmdHis', time() - ($clear_before_days * 86400) + zgap());
		$output = executeQuery('advanced_mailer.deleteLogs', $obj);
		
		if ($status === 'success')
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAdvanced_mailerAdminSentMail'));
		}
		else
		{
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAdvanced_mailerAdminErrors'));
		}
	}
	
	/**
	 * Send a test email using a temporary configuration.
	 */
	public function procAdvanced_MailerAdminTestSend()
	{
		$test_config = $this->getRequestVars();
		
		$recipient_config = Context::gets('recipient_name', 'recipient_email', 'use_exceptions');
		$recipient_name = $recipient_config->recipient_name;
		$recipient_email = $recipient_config->recipient_email;
		$use_exceptions = $recipient_config->use_exceptions === 'N' ? false : true;
		
		if (!class_exists('Mail'))
		{
			$this->add('test_result', 'Error: ' . Context::getLang('msg_advanced_mailer_cannot_find_mail_class'));
			return;
		}
		if (!method_exists('Mail', 'isAdvancedMailer') || !Mail::isAdvancedMailer())
		{
			$this->add('test_result', 'Error: ' . Context::getLang('msg_advanced_mailer_cannot_replace_mail_class'));
			return;
		}
		
		$validation = $this->validateConfiguration($test_config);
		if ($validation !== true)
		{
			$this->add('test_result', 'Error: ' . Context::getLang($validation));
			return;
		}
		
		if (!$recipient_name)
		{
			$this->add('test_result', 'Error: ' . Context::getLang('msg_advanced_mailer_recipient_name_is_empty'));
			return;
		}
		if (!$recipient_email)
		{
			$this->add('test_result', 'Error: ' . Context::getLang('msg_advanced_mailer_recipient_email_is_empty'));
			return;
		}
		if (!Mail::isVaildMailAddress($recipient_email))
		{
			$this->add('test_result', 'Error: ' . Context::getLang('msg_advanced_mailer_recipient_email_is_invalid'));
			return;
		}
		
		$previous_config = Mail::$config;
		Mail::$config = $test_config;
		
		try
		{
			$oMail = new Mail($use_exceptions ? false : $test_config->sending_method);
			$oMail->setTitle('Advanced Mailer Test : ' . strtoupper($oMail->getSendingMethod($recipient_email)));
			$oMail->setContent('<p>This is a <b>test email</b> from Advanced Mailer.</p><p>Thank you for trying Advanced Mailer.</p>' .
				'<p>고급 메일 발송 모듈 <b>테스트</b> 메일입니다.</p><p>메일이 정상적으로 발송되고 있습니다.</p>');
			$oMail->setReceiptor($recipient_name, $recipient_email);
			$result = $oMail->send();
			
			Mail::$config = $previous_config;
			if (!$result)
			{
				if (count($oMail->errors))
				{
					if ($test_config->sending_method === 'smtp')
					{
						if (strpos($test_config->smtp_host, 'gmail.com') !== false && strpos(implode("\n", $oMail->errors), 'code "535"') !== false)
						{
							$this->add('test_result', Context::getLang('msg_advanced_mailer_google_account_security'));
							return;
						}
						if (strpos($test_config->smtp_host, 'naver.com') !== false && strpos(implode("\n", $oMail->errors), 'Failed to authenticate') !== false)
						{
							$this->add('test_result', Context::getLang('msg_advanced_mailer_naver_smtp_disabled'));
							return;
						}
					}
					
					$this->add('test_result', nl2br(htmlspecialchars(implode("\n", $oMail->errors))));
					return;
				}
				else
				{
					$this->add('test_result', Context::getLang('msg_advanced_mailer_unknown_error'));
					return;
				}
			}
		}
		catch (Exception $e)
		{
			Mail::$config = $previous_config;
			$this->add('test_result', nl2br(htmlspecialchars($e->getMessage())));
			return;
		}
		
		$this->add('test_result', Context::getLang('msg_advanced_mailer_test_success'));
		return;
	}
	
	/**
	 * Get configuration from the current request.
	 */
	protected function getRequestVars()
	{
		$request_args = Context::getRequestVars();
		$args = $this->getConfig();
		$args->is_enabled = $request_args->is_enabled === 'N' ? 'N' : 'Y';
		$args->log_sent_mail = $request_args->log_sent_mail === 'Y' ? 'Y' : 'N';
		$args->log_errors = $request_args->log_errors === 'Y' ? 'Y' : 'N';
		$args->sending_method = trim($request_args->sending_method ?: 'mail');
		$args->sending_method = preg_replace('/\W/', '', $args->sending_method);
		foreach ($this->sending_methods as $sending_method => $sending_conf)
		{
			foreach ($sending_conf['conf'] as $conf_name)
			{
				$args->{$sending_method . '_' . $conf_name} = trim($request_args->{$sending_method . '_' . $conf_name} ?: '');
			}
		}
		$args->sender_name = trim($request_args->sender_name ?: '');
		$args->sender_email = trim($request_args->sender_email ?: '');
		$args->reply_to = trim($request_args->reply_to ?: '');
		$args->force_sender = $request_args->force_sender === 'Y' ? 'Y' : 'N';
		return $args;
	}
	
	/**
	 * Validate configuration from the current request.
	 */
	protected function validateConfiguration($args)
	{
		if ($args->is_enabled === 'N')
		{
			return true;
		}
		
		switch ($args->sending_method)
		{
			case 'dummy':
				break;
			
			case 'mail':
				break;
			
			case 'smtp':
				if (!$args->smtp_host || !preg_match('/^[a-z0-9.-]+$/', $args->smtp_host))
				{
					return 'msg_advanced_mailer_smtp_host_is_invalid';
				}
				if (!$args->smtp_port || !ctype_digit($args->smtp_port))
				{
					return 'msg_advanced_mailer_smtp_port_is_invalid';
				}
				if (!in_array($args->smtp_security, array('none', 'ssl', 'tls')))
				{
					return 'msg_advanced_mailer_smtp_security_is_invalid';
				}
				if (!$args->smtp_username)
				{
					return 'msg_advanced_mailer_username_is_empty';
				}
				if (!$args->smtp_password)
				{
					return 'msg_advanced_mailer_password_is_empty';
				}
				break;
				
			case 'ses':
				if (!$args->ses_region || !preg_match('/^[a-z0-9.-]+$/', $args->ses_region))
				{
					return 'msg_advanced_mailer_aws_region_is_invalid';
				}
				if (!$args->ses_access_key)
				{
					return 'msg_advanced_mailer_aws_access_key_is_empty';
				}
				if (!$args->ses_secret_key)
				{
					return 'msg_advanced_mailer_aws_secret_key_is_empty';
				}
				break;
				
			case 'mailgun':
			case 'woorimail':
				if (!$args->{$args->sending_method . '_domain'})
				{
					return 'msg_advanced_mailer_domain_is_empty';
				}
				if (!$args->{$args->sending_method . '_api_key'})
				{
					return 'msg_advanced_mailer_api_key_is_empty';
				}
				break;
				
			case 'mandrill':
			case 'postmark':
			case 'sparkpost':
				if (!$args->{$args->sending_method . '_api_key'})
				{
					return 'msg_advanced_mailer_api_key_is_empty';
				}
				break;
				
			case 'sendgrid':
				if (!$args->sendgrid_username)
				{
					return 'msg_advanced_mailer_username_is_empty';
				}
				if (!$args->sendgrid_password)
				{
					return 'msg_advanced_mailer_password_is_empty';
				}
				break;
				
			default:
				return 'msg_advanced_mailer_sending_method_is_invalid';
		}
		
		if (!$args->sender_name)
		{
			return 'msg_advanced_mailer_sender_name_is_empty';
		}
		if (!$args->sender_email)
		{
			return 'msg_advanced_mailer_sender_email_is_empty';
		}
		if (!Mail::isVaildMailAddress($args->sender_email))
		{
			return 'msg_advanced_mailer_sender_email_is_invalid';
		}
		if ($args->reply_to && !Mail::isVaildMailAddress($args->reply_to))
		{
			return 'msg_advanced_mailer_reply_to_is_invalid';
		}
		
		return true;
	}
}
