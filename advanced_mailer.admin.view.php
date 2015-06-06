<?php

class Advanced_MailerAdminView extends Advanced_Mailer
{
	public function dispAdvanced_MailerAdminConfig()
	{
		$member_config = getModel('module')->getModuleConfig('member');
		
		Context::set('advanced_mailer_config', $this->getConfig());
		Context::set('advanced_mailer_server_ip', $this->getServerIP());
		Context::set('webmaster_name', $member_config->webmaster_name ? $member_config->webmaster_name : 'webmaster');
		Context::set('webmaster_email', $member_config->webmaster_email);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('config');
	}
	
	public function getServerIP()
	{
		if(isset($_SESSION['advanced_mailer_ip_cache']) && $_SESSION['advanced_mailer_ip_cache'][1] > time() - 3600)
		{
			return $_SESSION['advanced_mailer_ip_cache'][0];
		}
		else
		{
			$ch = curl_init('http://icanhazip.com/');
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$ip = trim(curl_exec($ch));
			$ip = preg_match('/^[0-9]+(\.[0-9]+){3}$/', $ip) ? $ip : false;
			$_SESSION['advanced_mailer_ip_cache'] = array($ip, time());
			return $ip;
		}
	}
}
