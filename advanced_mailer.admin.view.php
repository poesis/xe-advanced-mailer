<?php

/**
 * @file advanced_mailer.admin.view.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Admin View
 */
class Advanced_MailerAdminView extends Advanced_Mailer
{
	/**
	 * Display the general configuration form.
	 */
	public function dispAdvanced_MailerAdminConfig()
	{
		$config = $this->getConfig();
		$member_config = getModel('module')->getModuleConfig('member');
		
		if(version_compare(PHP_VERSION, '5.3', '<'))
		{
			$available_sending_methods = array();
		}
		elseif(version_compare(PHP_VERSION, '5.4', '<'))
		{
			$available_sending_methods = $this->sending_methods_php53;
		}
		else
		{
			$available_sending_methods = array_keys($this->sending_methods);
		}
		
		Context::set('advanced_mailer_config', (array)$config);
		Context::set('available_sending_methods', $available_sending_methods);
		Context::set('sending_methods', $this->sending_methods);
		Context::set('sending_method', $config->sending_method);
		Context::set('webmaster_name', $member_config->webmaster_name ? $member_config->webmaster_name : 'webmaster');
		Context::set('webmaster_email', $member_config->webmaster_email);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('config');
	}
	
	/**
	 * Display the exception domains configuration form.
	 */
	public function dispAdvanced_MailerAdminExceptions()
	{
		$config = $this->getConfig();
		Context::set('advanced_mailer_config', (array)$config);
		
		if(version_compare(PHP_VERSION, '5.4', '<'))
		{
			$available_sending_methods = $this->sending_methods_php53;
		}
		else
		{
			$available_sending_methods = array_keys($this->sending_methods);
		}
		
		Context::set('available_sending_methods', $available_sending_methods);
		Context::set('sending_methods', $this->sending_methods);
		Context::set('sending_method', $config->sending_method);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('exceptions');
	}
	
	/**
	 * Display the SPF/DKIM setting guide.
	 */
	public function dispAdvanced_MailerAdminSpfDkim()
	{
		$config = $this->getConfig();
		Context::set('advanced_mailer_config', (array)$config);
		
		$this->sending_methods['mail']['spf'] = 'ip4:' . $this->getServerIP();
		Context::set('sending_methods', $this->sending_methods);
		Context::set('sending_method', $config->sending_method);
		Context::set('sending_domain', strpos($config->sender_email, '@') !== false ? substr(strrchr($config->sender_email, '@'), 1) : null);
		
		$used_methods = array($config->sending_method);
		$config->exceptions = $config->exceptions ?: array();
		foreach ($config->exceptions as $exception)
		{
			if ($exception['method'] !== 'default' && $exception['method'] !== $config->sending_method && count($exception['domains']))
			{
				$used_methods[] = $exception['method'];
			}
		}
		Context::set('used_methods', $used_methods);
		
		$used_methods_with_usable_spf = array();
		$used_methods_with_usable_dkim = array();
		foreach ($used_methods as $method)
		{
			if ($method === 'woorimail' && $config->woorimail_account_type === 'free') continue;
			if ($this->sending_methods[$method]['spf'])
			{
				$used_methods_with_usable_spf[$method] = $this->sending_methods[$method]['spf'];
			}
			if ($this->sending_methods[$method]['dkim'])
			{
				$used_methods_with_usable_dkim[$method] = $this->sending_methods[$method]['dkim'];
			}
		}
		Context::set('used_methods_with_usable_spf', $used_methods_with_usable_spf);
		Context::set('used_methods_with_usable_dkim', $used_methods_with_usable_dkim);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('spf_dkim');
	}
	
	/**
	 * Display the sent mail log.
	 */
	public function dispAdvanced_MailerAdminSentMail()
	{
		$obj = new stdClass();
		$obj->status = 'success';
		$obj->page = $page = Context::get('page') ?: 1;
		$maillog = executeQuery('advanced_mailer.getLogByType', $obj);
		$maillog = $maillog->toBool() ? $this->procMailLog($maillog->data) : array();
		Context::set('advanced_mailer_log', $maillog);
		Context::set('advanced_mailer_status', 'success');
		
		$paging = $this->procPaging('success', $page);
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('page_navigation', $paging->page_navigation);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('view_log');
	}
	
	/**
	 * Display the error log.
	 */
	public function dispAdvanced_MailerAdminErrors()
	{
		$obj = new stdClass();
		$obj->status = 'error';
		$obj->page = $page = Context::get('page') ?: 1;
		$maillog = executeQuery('advanced_mailer.getLogByType', $obj);
		$maillog = $maillog->toBool() ? $this->procMailLog($maillog->data) : array();
		Context::set('advanced_mailer_log', $maillog);
		Context::set('advanced_mailer_status', 'error');
		
		$paging = $this->procPaging('error', $page);
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('page_navigation', $paging->page_navigation);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('view_log');
	}
	
	/**
	 * Process mail log for display.
	 */
	public function procMailLog($log)
	{
		foreach($log as $item)
		{
			$from = explode("\n", $item->mail_from);
			foreach($from as &$fromitem)
			{
				if(preg_match('/^(.+) <([^>]+)>$/', $fromitem, $matches))
				{
					$fromitem = array($matches[2], $matches[1]);
				}
				else
				{
					$fromitem = array($fromitem, '');
				}
			}
			$item->mail_from = $from;
			
			$to = explode("\n", $item->mail_to);
			foreach($to as &$toitem)
			{
				if(preg_match('/^(.+?) <([^>]+)>$/', $toitem, $matches))
				{
					$toitem = array($matches[2], $matches[1]);
				}
				else
				{
					$toitem = array($toitem, '');
				}
			}
			$item->mail_to = $to;
		}
		
		return $log;
	}
	
	/**
	 * Process paging.
	 */
	public function procPaging($status, $page = 1)
	{
		$args = new stdClass;
		$args->status = $status;
		$count = executeQuery('advanced_mailer.countLogByType', $args);
		$total_count = $count->data->count;
		$total_page = max(1, ceil($total_count / 20));
		
		$output = $this->createObject();
		$output->total_count = $total_count;
		$output->total_page = $total_page;
		$output->page = $page;
		$output->page_navigation = new PageHandler($total_count, $total_page, $page, 10);
		return $output;
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
