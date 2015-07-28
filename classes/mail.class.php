<?php

namespace Advanced_Mailer;

/**
 * @file mail.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: Mail
 */
class Mail extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		try
		{
			$transport = \Swift_MailTransport::newInstance();
			$mailer = \Swift_Mailer::newInstance($transport);
			$result = $mailer->send($this->message, $this->errors);
			return (bool)$result;
		}
		catch(\Exception $e)		
		{
			$this->errors = array('Mail: ' . $e->getMessage());
			return false;
		}
	}
}
