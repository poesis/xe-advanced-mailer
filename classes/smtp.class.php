<?php

namespace Advanced_Mailer;

/**
 * @file smtp.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
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
		
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
