<?php

class Xternal_Mailer_Woorimail extends Xternal_Mailer_Base
{
	public function send()
	{
		$data = array(
			'title' => $this->message->getSubject(),
			'content' => $this->content,
			'sender_email' => '',
			'sender_nickname' => '',
			'receiver_email' => array(),
			'receiver_nickname' => array(),
			'member_regdate' => date('YmdHis'),
			'domain' => self::$config->username,
			'authkey' => self::$config->password,
			'wms_domain' => 'woorimail.com',
			'wms_nick' => 'NOREPLY',
			'type' => 'api',
			'mid' => 'auth_woorimail',
			'act' => 'dispWwapimanagerMailApi',
			'callback' => '',
			'is_sendok' => 'W',
		);
		
		$from = $this->message->getFrom();
		foreach($from as $email => $name)
		{
			$data['sender_email'] = $email;
			$data['sender_nickname'] = $name;
		}
		
		$to = $this->message->getTo();
		foreach($to as $email => $name)
		{
			$data['receiver_email'][] = $email;
			$data['receiver_nickname'][] = $name;
		}
		
		$data['receiver_email'] = implode(',', $data['receiver_email']);
		$data['receiver_nickname'] = implode(',', $data['receiver_nickname']);
		
		$url = 'https://woorimail.com:20080/index.php';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    	$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
