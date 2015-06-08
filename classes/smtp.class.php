<?php

namespace Advanced_Mailer;

class Smtp extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$smtp_host = self::$config->smtp_host;
		$smtp_port = self::$config->smtp_port;
		$smtp_security = self::$config->smtp_security === 'none' ? null : self::$config->smtp_security;
		
		$transport = \Swift_SmtpTransport::newInstance($smtp_host, $smtp_port, $smtp_security);
		$transport->setUsername(self::$config->username);
		$transport->setPassword(self::$config->password);
		
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
