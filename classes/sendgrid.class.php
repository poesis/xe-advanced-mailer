<?php

class Xternal_Mailer_Sendgrid extends Xternal_Mailer_Base
{
	public function send()
	{
		$sendgrid = new \SendGrid(self::$config->username, self::$config->password);
		$email = new \SendGrid\Email();
		$email->setSubject($this->message->getSubject());
		
		$from = $this->message->getFrom();
		foreach($from as $email => $name)
		{
			$email->setFrom($email, $name);
		}
		
		$to = $this->message->getTo();
		foreach($to as $email => $name)
		{
			$email->addTo($email, $name);
		}
		
		foreach($this->attachments as $original_filename => $filename)
		{
			$email->addAttachment($original_filename, $filename);
		}
		foreach($this->cidAttachments as $cid => $original_filename)
		{
			$email->addAttachment($original_filename, basename($filename), $cid);
		}
		
		if($this->content_type === 'html')
		{
			$email->setHtml($this->content);
		}
		else
		{
			$email->setBody($this->content);
		}
		
		try
		{
			$result = $sendgrid->send($email);
		}
		catch(\SendGrid\Exception $e)
		{
			$this->errors = array('SendGrid: Exception ' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}
		
		$result = $result->getBody();
		if($result['message'] === 'success')
		{
			return true;
		}
		else
		{
			$this->errors = array('SendGrid: ' . $result['message']);
			return false;
		}
	}
}
