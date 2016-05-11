<?php

namespace Advanced_Mailer;

/**
 * @file sendgrid.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: SendGrid
 */
class Sendgrid extends Base
{
	public $assembleMessage = false;
	
	public function send()
	{
		$sendgrid = new \SendGrid(self::$config->sendgrid_username, self::$config->sendgrid_password);
		$email = new \SendGrid\Email();
		$email->setSubject($this->message->getSubject());
		
		if ($from = $this->message->getFrom())
		{
			foreach($from as $address => $name)
			{
				$email->setFrom($address)->setFromName($name);
			}
		}
		if ($to = $this->message->getTo())
		{
			foreach($to as $address => $name)
			{
				$email->addTo($address)->addToName($name);
			}
		}
		if ($cc = $this->message->getCc())
		{
			foreach($cc as $address => $name)
			{
				$email->addCc($address);
			}
		}
		if ($bcc = $this->message->getBcc())
		{
			foreach($bcc as $address => $name)
			{
				$email->addBcc($address);
			}
		}
		$replyTo = $this->message->getReplyTo();
		if($replyTo)
		{
			reset($replyTo);
			$email->setReplyTo(key($replyTo));
		}
		$references = $this->message->getHeaders()->get('References');
		if(is_object($references)) $references = $references->toString();
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
