<?php

class Xternal_Mailer_Mailgun extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$domain = self::$config->username;
		$mailgun = new \Mailgun(self::$config->password);
		$result = $mailgun->sendMessage($domain, null, $this->message->toString());
		return $result;
	}
}
