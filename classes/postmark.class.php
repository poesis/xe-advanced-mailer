<?php

namespace Advanced_Mailer;

class Postmark extends Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$transport = \Openbuildings\Postmark\Swift_PostmarkTransport::newInstance(self::$config->password);
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
