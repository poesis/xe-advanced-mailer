<?php

namespace Advanced_Mailer;

class Postmark extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$transport = \Openbuildings\Postmark\Swift_PostmarkTransport::newInstance(self::$config->postmark_api_key);
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
