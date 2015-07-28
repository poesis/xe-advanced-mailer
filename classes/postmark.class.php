<?php

namespace Advanced_Mailer;

/**
 * @file postmark.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: Postmark
 */
class Postmark extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		try
		{
			$transport = \Openbuildings\Postmark\Swift_PostmarkTransport::newInstance(self::$config->postmark_api_key);
			$mailer = \Swift_Mailer::newInstance($transport);
			$result = $mailer->send($this->message, $this->errors);
			return (bool)$result;
		}
		catch(\Exception $e)		
		{
			$this->errors = array('Postmark: ' . $e->getMessage());
			return false;
		}
	}
}
