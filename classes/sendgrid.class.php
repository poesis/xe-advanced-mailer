<?php

class Xternal_Mailer_Sendgrid extends Xternal_Mailer_Base
{
	public function send()
	{
		$sendgrid = new \Sendgrid(self::$config->username, self::$config->password);
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
		
		$result = $sendgrid->send($email);
		return $result;
	}
}
