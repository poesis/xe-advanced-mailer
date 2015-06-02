<?php

class Advanced_MailerAdminController extends Advanced_Mailer
{
	public function procAdvanced_MailerAdminInsertConfig()
	{
		$config = $this->getRequestVars();
		
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
			$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'advanced_mailer', 'act', 'dispAdvanced_mailerAdminConfig'));
		}
	}
	
	public function procAdvanced_MailerAdminTestSend()
	{
		$test_config = $this->getRequestVars();
		$test_config->send_type = preg_replace('/\W/', '', $test_config->send_type);
		$new_class_name = 'Advanced_Mailer\\' . ucfirst($test_config->send_type);
		
		$member_config = getModel('module')->getModuleConfig('member');
		$sender_name = $member_config->webmaster_name ? $member_config->webmaster_name : 'webmaster';
		$sender_email = $member_config->webmaster_email;
		
		$recipient_config = Context::gets('recipient_name', 'recipient_email');
		$recipient_name = $recipient_config->recipient_name;
		$recipient_email = $recipient_config->recipient_email;
		
		if (!class_exists('Mail'))
		{
			$this->add('test_result', 'Error: Mail class not found.');
			return;
		}
		if (!method_exists('Mail', 'isAdvancedMailer') || !Mail::isAdvancedMailer())
		{
			$this->add('test_result', 'Error: Mail class was not replaced with Advanced Mailer.');
			return;
		}
		
		if (!class_exists($new_class_name))
		{
			if (file_exists(__DIR__ . '/classes/' . $test_config->send_type . '.class.php'))
			{
				include_once __DIR__ . '/classes/' . $test_config->send_type . '.class.php';
			}
		}
		if (!class_exists($new_class_name))
		{
			$this->add('test_result', 'Error: Invalid send type: ' . $test_config->send_type);
			return;
		}
		
		if (!$sender_name)
		{
			$this->add('test_result', 'Error: Sender name is empty.');
			return;
		}
		if (!$sender_email)
		{
			$this->add('test_result', 'Error: Sender email is empty.');
			return;
		}
		if (!Mail::isVaildMailAddress($sender_email))
		{
			$this->add('test_result', 'Error: Sender email is invalid.');
			return;
		}
		
		if (!$recipient_name)
		{
			$this->add('test_result', 'Error: Recipient name is empty.');
			return;
		}
		if (!$recipient_email)
		{
			$this->add('test_result', 'Error: Recipient email is empty.');
			return;
		}
		if (!Mail::isVaildMailAddress($recipient_email))
		{
			$this->add('test_result', 'Error: Recipient email is invalid.');
			return;
		}
		
		
		$previous_config = $new_class_name::$config;
		$new_class_name::$config = $test_config;
		
		try
		{
			$oMail = new $new_class_name();
			$oMail->setTitle('Advanced Mailer Test');
			$oMail->setContent('<p>This is a <b>test email</b> from Advanced Mailer.</p><p>Thank you for trying Advanced Mailer.</p>');
			$oMail->setSender($sender_name, $sender_email);
			$oMail->setReceiptor($recipient_name, $recipient_email);
			$result = $oMail->send();
			
			$new_class_name::$config = $previous_config;
			if (!$result)
			{
				if (count($oMail->errors))
				{
					$this->add('test_result', nl2br(htmlspecialchars(implode("\n", $oMail->errors))));
					return;
				}
				else
				{
					$this->add('test_result', 'An unknown error occurred.');
					return;
				}
			}
		}
		catch (Exception $e)
		{
			$new_class_name::$config = $previous_config;
			$this->add('test_result', nl2br(htmlspecialchars($e->getMessage())));
			return;
		}
		
		$this->add('test_result', 'Success!');
		return;
	}
	
	protected function getRequestVars()
	{
		$request_args = Context::getRequestVars();
		$args = new stdClass();
		$args->send_type = $request_args->send_type ?: 'mail';
		$args->smtp_host = $request_args->smtp_host ?: '';
		$args->smtp_port = $request_args->smtp_port ?: '';
		$args->smtp_security = $request_args->smtp_security ?: 'none';
		$args->username = $request_args->username ?: '';
		$args->password = $request_args->password ?: '';
		$args->domain = $request_args->domain ?: '';
		$args->api_key = $request_args->api_key ?: '';
		$args->aws_region = $request_args->aws_region ?: '';
		$args->aws_access_key = $request_args->aws_access_key ?: '';
		$args->aws_secret_key = $request_args->aws_secret_key ?: '';
		return $args;
	}
}
