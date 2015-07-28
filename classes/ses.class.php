<?php

namespace Advanced_Mailer;

/**
 * @file ses.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: Amazon SES
 */
class Ses extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$debug = array($this, 'debugCallback');
		$endpoint = 'https://email.' . strtolower(self::$config->ses_region) . '.amazonaws.com/';
		
		$transport = \Swift_AWSTransport::newInstance(self::$config->ses_access_key, self::$config->ses_secret_key);
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
