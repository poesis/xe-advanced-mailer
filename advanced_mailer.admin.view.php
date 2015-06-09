<?php

/**
 * @file advanced_mailer.admin.view.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
 * @brief Advanced Mailer Admin View
 */
class Advanced_MailerAdminView extends Advanced_Mailer
{
	/**
	 * Display the configuration form.
	 */
	public function dispAdvanced_MailerAdminConfig()
	{
		$config = $this->getConfig();
		$member_config = getModel('module')->getModuleConfig('member');
		
		Context::set('advanced_mailer_config', (array)$config);
		Context::set('advanced_mailer_server_ip', $this->getServerIP());
		Context::set('sending_methods', $this->sending_methods);
		Context::set('webmaster_name', $member_config->webmaster_name ? $member_config->webmaster_name : 'webmaster');
		Context::set('webmaster_email', $member_config->webmaster_email);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('config');
	}
	
	/**
	 * Get the public IPv4 address of the current server.
	 */
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
