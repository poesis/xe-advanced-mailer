<?php

namespace Advanced_Mailer;

/**
 * @file mail.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
 * @brief Advanced Mailer Transport: Mail
 */
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
