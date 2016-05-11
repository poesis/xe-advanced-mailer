<?php

namespace Advanced_Mailer;

/**
 * @file sparkpost.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: SparkPost
 */
class SparkPost extends Base
{
	public $assembleMessage = true;
	
	public function send()
	{
		// Compile the list of recipients.
		$recipients = array();
		if ($to = $this->message->getTo())
		{
			foreach($to as $address => $name)
			{
				$recipients[] = array('address' => array('name' => $name, 'email' => $address));
			}
		}
		if ($cc = $this->message->getCc())
		{
			foreach($cc as $address => $name)
			{
				$recipients[] = array('address' => array('name' => $name, 'email' => $address));
			}
		}
		if ($bcc = $this->message->getBcc())
		{
			foreach($bcc as $address => $name)
			{
				$recipients[] = array('address' => array('name' => $name, 'email' => $address));
			}
		}
		
		// Prepare data and options for Requests.
		$headers = array(
			'Authorization' => self::$config->sparkpost_api_key,
			'Content-Type' => 'application/json',
		);
		$data = json_encode(array(
			'options' => array(
				'transactional' => true,
			),
			'recipients' => $recipients,
			'content' => array(
				'email_rfc822' => $this->message->toString(),
			),
		));
		$options = array(
			'timeout' => 5,
			'useragent' => 'PHP',
		);
		
		// Attempt to connect to the API server.
		$this->errors = array();
		$request = \Requests::post('https://api.sparkpost.com/api/v1/transmissions', $headers, $data, $options);
		
		$result = json_decode($request->body);
		if (!$result)
		{
			$this->errors[] = 'SparkPost: API server responded with invalid data: ' . $request->body;
			return false;
		}
		
		if ($result->errors)
		{
			foreach ($result->errors as $error)
			{
				$this->errors[] = 'SparkPost: ' . $error->message . ': ' . $error->description . ' (code ' . $error->code . ')';
			}
		}
		
		if ($result->results)
		{
			return $result->results->total_accepted_recipients > 0 ? true : false;
		}
		else
		{
			return false;
		}
	}
}
