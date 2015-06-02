<?php

namespace Advanced_Mailer;

class Mail extends Base
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
