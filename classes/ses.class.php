<?php

namespace Advanced_Mailer;

class Ses extends Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$debug = array($this, 'debugCallback');
		$endpoint = 'https://email.' . strtolower(self::$config->aws_region) . '.amazonaws.com/';
		
		$transport = \Swift_AWSTransport::newInstance(self::$config->aws_access_key, self::$config->aws_secret_key);
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
