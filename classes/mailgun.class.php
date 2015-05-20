<?php

class Xternal_Mailer_Mailgun extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		try
		{
			$domain = self::$config->username;
			$args = array(
				'from' => $this->getSender(),
				'to' => $this->getReceiptor(),
				'subject' => $this->getTitle(),
			);
			$mailgun = new \Mailgun\Mailgun(self::$config->password);
			$result = $mailgun->sendMessage($domain, $args, $this->message->toString());
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
