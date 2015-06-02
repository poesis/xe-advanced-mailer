<?php

namespace Advanced_Mailer;

class Sendgrid extends Base
{
	public function send()
	{
		$sendgrid = new \SendGrid(self::$config->username, self::$config->password);
		$email = new \SendGrid\Email();
		$email->setSubject($this->message->getSubject());
		
		$from = $this->message->getFrom();
		foreach($from as $address => $name)
		{
			$email->setFrom($address)->setFromName($name);
		}
		
		$to = $this->message->getTo();
		foreach($to as $address => $name)
		{
			$email->addTo($address)->addToName($name);
		}
		$cc = $this->message->getCc();
		foreach($cc as $address => $name)
		{
			$email->addCc($address);
		}
		$bcc = $this->message->getBcc();
		foreach($bcc as $address => $name)
		{
			$email->addBcc($address);
		}
		$replyTo = $this->message->getReplyTo();
		if(strval($replyTo) !== '')
		{
			$email->setReplyTo($replyTo);
		}
		$references = $this->message->getHeaders()->get('References')->toString();
		if(strlen(trim($references)) > 12)
		{
			$email->addHeader('References', substr($references, 12));
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
