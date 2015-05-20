<?php

class Xternal_Mailer_Mail extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$transport = \Swift_MailTransport::newInstance();
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
