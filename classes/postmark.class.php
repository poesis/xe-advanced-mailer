<?php

class Xternal_Mailer_Postmark extends Xternal_Mailer_Base
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
