<?php

class Xternal_Mailer_Mandrill extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$mandrill = new \Mandrill(self::$config->password);
		$result = $mandrill->messages->sendRaw($this->message->toString());
		return $result;
	}
}
