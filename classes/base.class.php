<?php

class Xternal_Mailer_Base
{
	/**
	 * Properties for compatibility
	 */
	public $content = '';
	public $content_type = 'html';
	public $attachments = array();
	public $cidAttachments = array();
	
	/**
	 * Properties used by Xternal Mailer
	 */
	public static $config = array();
	public $errors = array();
	public $message = NULL;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->message = \Swift_Message::newInstance();
	}
	
	/**
	 * Set parameters for using Gmail
	 */
	public function useGmailAccount()
	{
		// no-op
	}
	
	/**
	 * Set parameters for using SMTP protocol
	 */
	public function useSMTP()
	{
		// no-op
	}
	
	/**
	 * Set additional parameters
	 */
	public function setAdditionalParams($additional_params)
	{
		// no-op
	}
	
	/**
	 * Set Sender (From:)
	 *
	 * @param string $name Sender name
	 * @param string $email Sender email address
	 * @return void
	 */
	public function setSender($name, $email)
	{
		$this->message->setFrom(array($email => $name));
	}
	
	/**
	 * Get Sender (From:)
	 *
	 * @return string
	 */
	public function getSender()
	{
		$from = $this->message->getFrom();
		foreach($from as $email => $name)
		{
			if($name === '')
			{
				return $email;
			}
			else
			{
				return $name . ' <' . $email . '>';
			}
		}
		return FALSE;
	}
	
	/**
	 * Set Receiptor (TO:)
	 *
	 * @param string $name Receiptor name
	 * @param string $email Receiptor email address
	 * @return void
	 */
	public function setReceiptor($name, $email)
	{
		$this->message->setTo(array($email => $name));
	}
	
	/**
	 * Get Receiptor (TO:)
	 *
	 * @return string
	 */
	public function getReceiptor()
	{
		$to = $this->message->getTo();
		foreach($to as $email => $name)
		{
			if($name === '')
			{
				return $email;
			}
			else
			{
				return $name . ' <' . $email . '>';
			}
		}
		return FALSE;
	}
	
	/**
	 * Set Email's Title
	 *
	 * @param string $title Title to set
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->message->setSubject($title);
	}
	
	/**
	 * Get Email's Title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->message->getSubject();
	}
	
	/**
	 * Set BCC
	 *
	 * @param string $bcc
	 * @return void
	 */
	public function setBCC($bcc)
	{
		$this->message->setBcc(array($bcc));
	}
	
	/**
	 * Set ReplyTo param
	 *
	 * @param string $replyTo
	 * @return void
	 */
	public function setReplyTo($replyTo)
	{
		$this->message->setReplyTo(array($replyTo));
	}
	
	/**
	 * Set Message ID
	 *
	 * @param string $messageId
	 * @return void
	 */
	public function setMessageID($messageId)
	{
		$this->message->getHeaders()->get('Message-ID')->setId($messageId);
	}
	
	/**
	 * Set references
	 *
	 * @param string $references
	 * @return void
	 */
	public function setReferences($references)
	{
		$headers = $this->message->getHeaders();
		$headers->addTextHeader('References', $references);
	}
	
	/**
	 * Set message content
	 *
	 * @param string $content Content
	 * @return void
	 */
	public function setContent($content)
	{
		$content = preg_replace_callback('/<img([^>]+)>/i', array($this, 'replaceResourceRealPath'), $content);
		$this->content = $content;
	}
	
	/**
	 * Set the type of body's content
	 */
	public function setContentType($mode = 'html')
	{
		$this->content_type = $mode === 'html' ? 'html' : '';
	}
	
	/**
	 * Get the Plain content of body message
	 *
	 * @return string
	 */
	public function getPlainContent()
	{
		return chunk_split(base64_encode(str_replace(array("<", ">", "&"), array("&lt;", "&gt;", "&amp;"), $this->content)));
	}
	
	/**
	 * Get the HTML content of body message
	 */
	public function getHTMLContent()
	{
		return chunk_split(base64_encode($this->content_type != 'html' ? nl2br($this->content) : $this->content));
	}
	
	/**
	 * Add file attachment
	 *
	 * @param string $filename File name to attach
	 * @param string $original_filename Real path of file to attach
	 * @return void
	 */
	public function addAttachment($filename, $original_filename)
	{
		$this->attachments[$original_filename] = $filename;
	}
	
	/**
	 * Add content attachment
	 *
	 * @param string $original_filename Real path of file to attach
	 * @param string $cid Content-CID
	 * @return void
	 */
	public function addCidAttachment($original_filename, $cid)
	{
		$this->cidAttachments[$cid] = $original_filename;
	}
	
	/**
	 * Replace resourse path of the files
	 *
	 * @see Mail::setContent()
	 * @param array $matches Match info.
	 * @return string
	 */
	public function replaceResourceRealPath($matches)
	{
		return preg_replace('/src=(["\']?)files/i', 'src=$1' . Context::getRequestUri() . 'files', $matches[0]);
	}
	
	/**
	 * Process the images from attachments
	 *
	 * @return void
	 */
	public function procAttachments()
	{
		// no-op
	}
	
	/**
	 * Process the images from body content. This functions is used if Mailer is set as mail not as SMTP
	 */
	public function procCidAttachments()
	{
		// no-op
	}
	
	/**
	 * Process the message before sending
	 */
	public function procAssembleMessage()
	{
		foreach($this->attachments as $original_filename => $filename)
		{
			$attachment = \Swift_Attachment::fromPath($original_filename);
			$attachment->setFilename($filename);
			$this->message->attach($attachment);
		}
		foreach($this->cidAttachments as $cid => $original_filename)
		{
			$embedded = \Swift_EmbeddedFile::fromPath($original_filename);
			$newcid = $this->message->embed($embedded);
			$this->content = str_replace(array("cid:$cid", $cid), $newcid, $this->content);
		}
		$content_type = $this->content_type === 'html' ? 'text/html' : 'text/plain';
		$this->message->setBody($this->content, $content_type);
	}
	
	/**
	 * Send email
	 */
	public function send()
	{
		$this->procAssembleMessage();
		
		$transport = \Swift_NullTransport::newInstance();
		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this->message, $this->errors);
		return (bool)$result;
	}
	
	/**
	 * Check if DNS of param is real or fake
	 */
	public function checkMailMX($email_address)
	{
		if(!Mail::isVaildMailAddress($email_address))
		{
			return FALSE;
		}
		list($user, $host) = explode("@", $email_address);
		if(function_exists('checkdnsrr'))
		{
			if(checkdnsrr($host, "MX") || checkdnsrr($host, "A"))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Check if param is a valid email or not
	 */
	public function isVaildMailAddress($email_address)
	{
		if(preg_match("/([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/i", $email_address))
		{
			return $email_address;
		}
		else
		{
			return '';
		}
	}
}
