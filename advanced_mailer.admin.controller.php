<?php

class Advanced_MailerAdminController extends Advanced_Mailer
{
	public function procAdvanced_MailerAdminInsertConfig()
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
		
		$output = getController('module')->insertModuleConfig('advanced_mailer', $args);
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
}
