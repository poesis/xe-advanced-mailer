<?php

class Xternal_Mailer_Ses extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$transport = \Swift_AWSTransport::newInstance(self::$config->username, self::$config->password);
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
