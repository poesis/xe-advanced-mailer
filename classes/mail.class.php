<?php

namespace Advanced_Mailer;

class Mail extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$transport = \Swift_MailTransport::newInstance();
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
}
