<?php

class Xternal_Mailer_Mandrill extends Xternal_Mailer_Base
{
	public function send()
	{
		$this->procAssembleMessage();
		
		try
		{
			$mandrill = new \Mandrill(self::$config->password);
			$result = $mandrill->messages->sendRaw($this->message->toString());
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
		return count($this->errors) ? true : false;
	}
}
