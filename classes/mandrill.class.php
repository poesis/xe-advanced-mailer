<?php

namespace Advanced_Mailer;

/**
 * @file mandrill.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: Mandrill
 */
class Mandrill extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$recipients = array();
		if ($to = $this->message->getTo())
		{
			foreach($to as $address => $name)
			{
				$recipients[] = $address;
			}
		}
		if ($cc = $this->message->getCc())
		{
			foreach($cc as $address => $name)
			{
				$recipients[] = $address;
			}
		}
		if ($bcc = $this->message->getBcc())
		{
			foreach($bcc as $address => $name)
			{
				$recipients[] = $address;
			}
		}
		
		try
		{
			$mandrill = new \Mandrill(self::$config->mandrill_api_key);
			$result = $mandrill->messages->sendRaw($this->message->toString(), null, null, $recipients);
		}
		catch(\Mandrill_Error $e)
		{
			$this->errors = array(get_class($e) . ': ' . $e->getMessage());
			return false;
		}
		
		$this->errors = array();
		foreach($result as $item)
		{
			if($item['status'] === 'rejected' || $item['status'] === 'invalid')
			{
				$this->errors[] = 'Mandrill: ' . $item['email'] . ' - ' . $item['status'] . ' (' . $item['reject_reason'] . ')';
			}
		}
		return count($this->errors) ? false : true;
	}
}
