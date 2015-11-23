<?php

namespace Advanced_Mailer;

/**
 * @file smtp.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: SMTP
 */
class Smtp extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$smtp_host = self::$config->smtp_host;
		$smtp_port = self::$config->smtp_port;
		$smtp_security = self::$config->smtp_security === 'none' ? null : self::$config->smtp_security;
		
		$transport = \Swift_SmtpTransport::newInstance($smtp_host, $smtp_port, $smtp_security);
		$transport->setUsername(self::$config->smtp_username);
		$transport->setPassword(self::$config->smtp_password);
		
		$local_domain = $transport->getLocalDomain();
		if (preg_match('/^\*\.(.+)$/', $local_domain, $matches))
		{
			$transport->setLocalDomain($matches[1]);
		}
		
		try
		{
			$mailer = \Swift_Mailer::newInstance($transport);
			$result = $mailer->send($this->message, $this->errors);
			return (bool)$result;
		}
		catch(\Exception $e)		
		{
			$this->errors = array('SMTP: ' . $e->getMessage());
			return false;
		}
	}
}
