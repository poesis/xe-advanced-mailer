<?php

namespace Advanced_Mailer;

class Mandrill extends Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		$recipients = array();
		$to = $this->message->getTo();
		foreach($to as $address => $name)
		{
			$recipients[] = $address;
		}
		$cc = $this->message->getCc();
		foreach($cc as $address => $name)
		{
			$recipients[] = $address;
		}
		$bcc = $this->message->getBcc();
		foreach($bcc as $address => $name)
		{
			$recipients[] = $address;
		}
		
		try
		{
			$mandrill = new \Mandrill(self::$config->api_key);
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
