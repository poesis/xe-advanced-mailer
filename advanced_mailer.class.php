<?php

class Advanced_Mailer extends ModuleObject
{
	public $sending_methods = array(
		'mail' => array(
			'conf' => array(),
			'spf' => '',
			'dkim' => '',
		),
		'smtp' => array(
			'conf' => array('host', 'port', 'security', 'username', 'password'),
			'spf' => '',
			'dkim' => '',
		),
		'ses' => array(
			'conf' => array('region', 'access_key', 'secret_key'),
			'spf' => '',
			'dkim' => '',
		),
		'mailgun' => array(
			'conf' => array('domain', 'api_key'),
			'spf' => 'include:mailgun.org',
			'dkim' => 'mailo._domainkey',
		),
		'mandrill' => array(
			'conf' => array('api_key'),
			'spf' => 'include:spf.mandrillapp.com',
			'dkim' => 'mandrill._domainkey',
		),
		'postmark' => array(
			'conf' => array('api_key'),
			'spf' => 'include:spf.mtasv.net',
			'dkim' => '********.pm._domainkey',
		),
		'sendgrid' => array(
			'conf' => array('username', 'password'),
			'spf' => 'include:sendgrid.net',
			'dkim' => 'smtpapi._domainkey',
		),
		'woorimail' => array(
			'conf' => array('domain', 'api_key', 'account_type'),
			'spf' => 'include:woorimail.com',
			'dkim' => '',
		),
	);
	
	public function getConfig()
	{
		// Get the configuration or initialize it.
		$config = getModel('module')->getModuleConfig('advanced_mailer');
		if(!is_object($config))
		{
			$config = new stdClass();
			$config->sending_method = 'mail';
		}
		
		// Migrate from previous configuration format.
		if(isset($config->send_type) || isset($config->api_key))
		{
			if(isset($config->send_type))
			{
				$config->sending_method = $config->send_type;
				unset($config->send_type);
			}
			
			if(isset($config->username))
			{
				if(in_array('username', $this->sending_methods[$config->sending_method]['conf']))
				{
					$config->{$config->sending_method . '_username'} = $config->username;
				}
				unset($config->username);
			}
			
			if(isset($config->password))
			{
				if(in_array('password', $this->sending_methods[$config->sending_method]['conf']))
				{
					$config->{$config->sending_method . '_password'} = $config->password;
				}
				unset($config->password);
			}
			
			if(isset($config->domain))
			{
				if(in_array('domain', $this->sending_methods[$config->sending_method]['conf']))
				{
					$config->{$config->sending_method . '_domain'} = $config->domain;
				}
				unset($config->domain);
			}
			
			if(isset($config->api_key))
			{
				if(in_array('api_key', $this->sending_methods[$config->sending_method]['conf']))
				{
					$config->{$config->sending_method . '_api_key'} = $config->api_key;
				}
				unset($config->api_key);
			}
			
			if(isset($config->account_type))
			{
				if(in_array('account_type', $this->sending_methods[$config->sending_method]['conf']))
				{
					$config->{$config->sending_method . '_account_type'} = $config->account_type;
				}
				unset($config->account_type);
			}
			
			if(isset($config->aws_region))
			{
				$config->ses_region = $config->aws_region;
				unset($config->aws_region);
			}
			
			if(isset($config->aws_access_key))
			{
				$config->ses_access_key = $config->aws_access_key;
				unset($config->aws_access_key);
			}
			
			if(isset($config->aws_secret_key))
			{
				$config->ses_secret_key = $config->aws_secret_key;
				unset($config->aws_secret_key);
			}
		}
		
		return $config;
	}
	
	public function moduleInstall()
	{
		$oModuleController = getController('module');
		$oModuleController->insertTrigger('moduleHandler.init', 'advanced_mailer', 'model', 'triggerReplaceMailClass', 'before');
		return new Object();
	}
	
	public function checkUpdate()
	{
		return false;
	}
	
	public function moduleUpdate()
	{
		return new Object(0, 'success_updated');
	}
	
	public function recompileCache()
	{
		// no-op
	}
}
