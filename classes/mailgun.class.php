<?php

namespace Advanced_Mailer;

/**
 * @file mailgun.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
 * @brief Advanced Mailer Transport: Mailgun
 */
class Mailgun extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		$args = array(
			'subject' => $this->getTitle(),
			'from' => $this->getSender(),
			'to' => array(),
			'cc' => array(),
			'bcc' => array(),
		);
		$replyTo = $this->message->getReplyTo();
		if(count($replyTo))
		{
			reset($replyTo);
			$args['h:Reply-To'] = key($replyTo);
		}
		$to = $this->message->getTo();
		foreach($to as $address => $name)
		{
			$args['to'][] = $address;
		}
		$cc = $this->message->getCc();
		foreach($cc as $address => $name)
		{
			$args['cc'][] = $address;
		}
		$bcc = $this->message->getBcc();
		foreach($bcc as $address => $name)
		{
			$args['to'][] = $address;
		}
		$args['to'] = implode(', ', $args['to']);
		$args['cc'] = implode(', ', $args['cc']);
		$args['bcc'] = implode(', ', $args['bcc']);
		
		try
		{
			$mailgun = new \Mailgun\Mailgun(self::$config->mailgun_api_key);
			$result = $mailgun->sendMessage(self::$config->mailgun_domain, $args, $this->message->toString());
		}
		catch(\Exception $e)
		{
			$this->errors = array(get_class($e) . ': ' . $e->getMessage());
			return false;
		}
		
		$response_code = intval($result->http_response_code);
		if($response_code === 200)
		{
			return true;
		}
		else
		{
			$this->errors = array('Mailgun: server returned code ' . $response_code);
			if(isset($result->http_response_body->items))
			{
				foreach($result->http_response_body->items as $item)
				{
					$this->errors[] = $item->message;
				}
			}
			return false;
		}
	}
}
