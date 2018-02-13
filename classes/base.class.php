<?php

namespace Advanced_Mailer;

/**
 * @file base.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Base Class
 */
class Base
{
	/**
	 * Properties for compatibility with XE Mail class
	 */
	public $content = '';
	public $content_type = 'html';
	public $attachments = array();
	public $cidAttachments = array();
	
	/**
	 * Properties used by Advanced Mailer
	 */
	public static $config = array();
	public $errors = array();
	public $caller = NULL;
	public $message = NULL;
	public $assembleMessage = true;
	public $forceSendingMethod = false;
	
	/**
	 * Constructor
	 */
	public function __construct($force_sending_method = null)
	{
		// Load SwiftMailer
		if(version_compare(PHP_VERSION, '5.4', '<'))
		{
			include_once dirname(__DIR__) . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';
		}
		else
		{
			include_once dirname(__DIR__) . '/vendor/autoload.php';
		}
		$this->message = \Swift_Message::newInstance();
		
		// Force sending method
		if($force_sending_method !== null)
		{
			$this->forceSendingMethod = $force_sending_method;
		}
		
		// Auto-fill the sender info
		if(self::$config->sender_email)
		{
			try
			{
				$sender_name = self::$config->sender_name ?: 'webmaster';
				$this->message->setFrom(array(self::$config->sender_email => $sender_name));
			}
			catch (\Exception $e)
			{
				$this->errors[] = array($e->getMessage());
			}
		}
		
		// Auto-fill the Reply-To address
		if(self::$config->reply_to)
		{
			try
			{
				$this->message->setReplyTo(array(self::$config->reply_to));
			}
			catch (\Exception $e)
			{
				$this->errors[] = array($e->getMessage());
			}
		}
	}
	
	/**
	 * Method for checking whether this class is from Advanced Mailer
	 */
	public function isAdvancedMailer()
	{
		return true;
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
		if (self::$config->force_sender === 'Y' && self::$config->sender_email)
		{
			$this->setReplyTo($email);
			$email = self::$config->sender_email;
		}
		
		try
		{
			$this->message->setFrom(array($email => $name));
		}
		catch (\Exception $e)
		{
			$this->errors[] = array($e->getMessage());
		}
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
	 * Set Recipient (To:)
	 *
	 * @param string $name Recipient name
	 * @param string $email Recipient email address
	 * @return void
	 */
	public function setReceiptor($name, $email)
	{
		try
		{
			$this->message->setTo(array($email => $name));
		}
		catch (\Exception $e)
		{
			$this->errors[] = array($e->getMessage());
		}
	}
	
	/**
	 * Get Recipient (To:)
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
	 * Set Subject
	 *
	 * @param string $subject The subject
	 * @return void
	 */
	public function setTitle($subject)
	{
		$this->message->setSubject(strval($subject));
	}
	
	/**
	 * Get Subject
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
		try
		{
			$this->message->setBcc(array($bcc));
		}
		catch (\Exception $e)
		{
			$this->errors[] = array($e->getMessage());
		}
	}
	
	/**
	 * Set ReplyTo
	 *
	 * @param string $replyTo
	 * @return void
	 */
	public function setReplyTo($replyTo)
	{
		try
		{
			$this->message->setReplyTo(array($replyTo));
		}
		catch (\Exception $e)
		{
			$this->errors[] = array($e->getMessage());
		}
	}
	
	/**
	 * Set Return Path
	 *
	 * @param string $returnPath
	 * @return void
	 */
	public function setReturnPath($returnPath)
	{
		try
		{
			$this->message->setReturnPath($returnPath);
		}
		catch (\Exception $e)
		{
			$this->errors[] = array($e->getMessage());
		}
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
	 * Set the type of message content (html or plain text)
	 * 
	 * @param string $mode The type
	 * @return void
	 */
	public function setContentType($type = 'html')
	{
		$this->content_type = $type === 'html' ? 'html' : '';
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
	 * 
	 * @return string
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
		if (preg_match('/[\\/\\\\]/', $filename))
		{
			$basename = preg_match('/[\\/\\\\]([^\\/\\\\]+)$/', $original_filename, $matches) ? $matches[1] : $original_filename;
			$original_filename = $filename;
			$filename = $basename;
		}
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
		return preg_replace('/src=(["\']?)files/i', 'src=$1' . \Context::getRequestUri() . 'files', $matches[0]);
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
	 * 
	 * @return void
	 */
	public function procCidAttachments()
	{
		// no-op
	}
	
	/**
	 * Process the message before sending
	 * 
	 * @return void
	 */
	public function procAssembleMessage()
	{
		// Clear previous attachments
		$children = $this->message->getChildren();
		foreach($children as $key => $val)
		{
			if($val instanceof \Swift_Mime_Attachment)
			{
				unset($children[$key]);
			}
		}
		$this->message->setChildren(array_values($children));
		
		// Add all attachments
		foreach($this->attachments as $original_filename => $filename)
		{
			$attachment = \Swift_Attachment::fromPath($original_filename);
			$attachment->setFilename($filename);
			$this->message->attach($attachment);
		}
		
		// Add all CID attachments
		foreach($this->cidAttachments as $cid => $original_filename)
		{
			$embedded = \Swift_EmbeddedFile::fromPath($original_filename);
			$newcid = $this->message->embed($embedded);
			$this->content = str_replace(array("cid:$cid", $cid), $newcid, $this->content);
		}
		
		// Set content type
		$content_type = $this->content_type === 'html' ? 'text/html' : 'text/plain';
		$this->message->setBody($this->content, $content_type);
	}
	
	/**
	 * Send email
	 * 
	 * @return bool
	 */
	public function send()
	{
		// Get caller information
		$backtrace = debug_backtrace(0);
		if(count($backtrace) && isset($backtrace[0]['file']))
		{
			$this->caller = $backtrace[0]['file'] . ($backtrace[0]['line'] ? (' line ' . $backtrace[0]['line']) : '');
		}
		
		// Get the currently configured sending method
		$sending_method = self::$config->sending_method;
		
		// Check whether an exception should be used
		$to = $this->message->getTo();
		reset($to); $to_email = key($to);
		if($to_email !== null && $to_email !== false)
		{
			$sending_method = $this->getSendingMethod($to_email);
		}
		
		// Reset Message-ID
		$random = substr(hash('sha256', mt_rand() . microtime() . getmypid()), 0, 32);
		$sender = $this->message->getFrom(); reset($sender);
		$id = $random . '@' . (preg_match('/^(.+)@([^@]+)$/', key($sender), $matches) ? $matches[2] : 'swift.generated');
		$this->message->getHeaders()->get('Message-ID')->setId($id);
		
		// Create a copy of the email using the sending method
		include_once __DIR__ . '/' . strtolower($sending_method) . '.class.php';
		$subclass_name = __NAMESPACE__ . '\\' . ucfirst($sending_method);
		$subclass = new $subclass_name();
		$data = get_object_vars($this);
		foreach($data as $key => $value)
		{
			$subclass->$key = $value;
		}
		
		// Call the 'before' trigger
		$output = \ModuleHandler::triggerCall('advanced_mailer.send', 'before', $subclass);
		if(!$output->toBool()) return $output;
		
		try
		{
			// Assemble all attachments
			if($subclass->assembleMessage)
			{
				$subclass->procAssembleMessage();
			}
			
			// Send the email and retrieve any errors
			$result = $subclass->send();
			foreach ($subclass->errors as $error)
			{
				$this->errors[] = $error;
			}
		}
		catch (\Exception $e)
		{
			$result = false;
			$this->errors[] = array($e->getMessage());
		}
		
		// Call the 'after' trigger
		$output = \ModuleHandler::triggerCall('advanced_mailer.send', 'after', $subclass);
		if(!$output->toBool()) return $output;
		
		// Log this mail
		if(self::$config->log_sent_mail === 'Y' || (self::$config->log_errors === 'Y' && count($this->errors)))
		{
			$obj = new \stdClass();
			$obj->mail_srl = getNextSequence();
			$obj->mail_from = '';
			$obj->mail_to = '';
			
			if ($real_sender = $subclass->message->getFrom())
			{
				foreach($real_sender as $email => $name)
				{
					$obj->mail_from .= (strval($name) !== '' ? "$name <$email>" : $email) . "\n";
				}
			}
			
			if ($real_to = $subclass->message->getTo())
			{
				foreach($real_to as $email => $name)
				{
					$obj->mail_to .= (strval($name) !== '' ? "$name <$email>" : $email) . "\n";
				}
			}
			
			if ($real_cc = $subclass->message->getCc())
			{
				foreach($real_cc as $email => $name)
				{
					$obj->mail_to .= (strval($name) !== '' ? "$name <$email>" : $email) . "\n";
				}
			}
			
			if ($real_bcc = $subclass->message->getBcc())
			{
				foreach($real_bcc as $email => $name)
				{
					$obj->mail_to .= (strval($name) !== '' ? "$name <$email>" : $email) . "\n";
				}
			}
			
			$obj->mail_from = trim($obj->mail_from);
			$obj->mail_to = trim($obj->mail_to);
			$obj->subject = $subclass->message->getSubject();
			$obj->calling_script = $this->caller;
			$obj->sending_method = $sending_method;
			$obj->status = $result ? 'success' : 'error';
			$obj->errors = count($this->errors) ? implode("\n", $this->errors) : null;
			$output = executeQuery('advanced_mailer.insertLog', $obj);
			if(!$output->toBool()) return $output;
		}
		
		// Return the result (bool)
		return $result;
	}
	
	/**
	 * Get sending method for email address
	 */
	public function getSendingMethod($email = null)
	{
		if($this->forceSendingMethod)
		{
			return $this->forceSendingMethod;
		}
		
		if($email === null)
		{
			return self::$config->sending_method;
		}
		
		$domain = strpos($email, '@') !== false ? strtolower(substr(strrchr($email, '@'), 1)) : null;
		if($domain === null)
		{
			return self::$config->sending_method;
		}
		
		if(strpos($domain, 'xn--') !== false) $domain = idn_to_utf8($domain);
		
		if(is_array(self::$config->exceptions))
		{
			foreach(self::$config->exceptions as $exception)
			{
				if($exception['method'] === 'default') continue;
				if(in_array($domain, $exception['domains'], true))
				{
					return $exception['method'];
				}
			}
		}
		
		return self::$config->sending_method;
	}
	
	/**
	 * Get caller info
	 * 
	 * @return string
	 */
	public function getCaller()
	{
		return $this->caller;
	}
	
	/**
	 * Get errors
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
	
	/**
	 * Check if DNS of param is real or fake
	 * 
	 * @param string $email_address Email address to check
	 * @return bool
	 */
	public function checkMailMX($email_address)
	{
		if(!self::isVaildMailAddress($email_address))
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
	 * 
	 * @param string $email_address Email address to check
	 * @return string
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
