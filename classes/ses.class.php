<?php

class Xternal_Mailer_Ses extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$debug = array($this, 'debugCallback');
		$endpoint = 'https://email.us-east-1.amazonaws.com/';
		if(preg_match('/^(us|eu|ap)-(north|south)?(east|west)-[0-9a-z]+$/i', self::$config->smtp_host))
		{
			$endpoint = 'https://email.' . strtolower(self::$config->smtp_host) . '.amazonaws.com/';
		}
		
		$transport = \Swift_AWSTransport::newInstance(self::$config->username, self::$config->password);
		$transport->setDebug($debug);
		$transport->setEndpoint($endpoint);
		
		try
		{
			$mailer = \Swift_Mailer::newInstance($transport);
			$result = $mailer->send($this->message, $this->errors);
		}
		catch(\Exception $e)
		{
			$this->errors = array('Amazon SES: ' . $e->getMessage());
			return false;
		}
		
		if($result)
		{
			$this->errors = array();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function debugCallback($msg)
	{
		$this->errors[] = $msg;
	}
}
