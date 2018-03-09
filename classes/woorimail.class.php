<?php

namespace Advanced_Mailer;

/**
 * @file woorimail.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @brief Advanced Mailer Transport: Woorimail
 */
class Woorimail extends Base
{
	public static $_error_codes = array(
		'me_001' => '@ 없는 이메일 주소가 있습니다.',
		'me_002' => '이메일 주소가 존재하지 않습니다.',
		'me_003' => '닉네임이 존재하지 않습니다.',
		'me_004' => '등록일이 존재하지 않습니다.',
		'me_005' => '이메일과 닉네임 갯수가 다릅니다.',
		'me_006' => '닉네임과 등록일 갯수가 다릅니다.',
		'me_007' => '이메일과 등록일 갯수가 다릅니다.',
		'me_008' => '이메일 갯수가 2,000개 넘습니다.',
		'me_009' => 'type이 api가 아닙니다.',
		'me_010' => '인증키가 없습니다.',	
		'me_011' => '인증키가 부정확합니다.',
		'me_012' => '포인트가 부족합니다.',
		'me_013' => '전용채널에 도메인이 등록되어 있지 않습니다.',
	);
	
	public $assembleMessage = false;
	
	public function send()
	{
		$data = array(
			'title' => $this->message->getSubject(),
			'content' => $this->content,
			'sender_email' => '',
			'sender_nickname' => '',
			'receiver_email' => array(),
			'receiver_nickname' => array(),
			'member_regdate' => array(),
			'domain' => self::$config->woorimail_domain,
			'authkey' => self::$config->woorimail_api_key,
			'wms_domain' => 'woorimail.com',
			'wms_nick' => 'NOREPLY',
			'type' => 'api',
			'mid' => 'auth_woorimail',
			'act' => 'dispWwapimanagerMailApi',
			'callback' => '',
			'is_sendok' => 'W',
		);

		$from_name = '';
		$from_email = '';
		$wms_email = '';
		
		if ($from = $this->message->getFrom())
		{
			reset($from);
			$from_email = $wms_email = key($from);
			$from_name = current($from);
		}
		if (self::$config->force_sender === 'Y' && $from_email === self::$config->sender_email)
		{
			if ($replyTo = $this->message->getReplyTo())
			{
				reset($replyTo);
				$from_email = key($replyTo);
			}
		}
		
		$data['sender_email'] = $from_email;
		$data['sender_nickname'] = $from_name;
		if (self::$config->woorimail_account_type === 'paid')
		{
			$wms_email = explode('@', $wms_email);
			$data['wms_nick'] = $wms_email[0];
			$data['wms_domain'] = $wms_email[1];
		}
		
		if ($to = $this->message->getTo())
		{
			foreach($to as $email => $name)
			{
				$data['receiver_email'][] = $email;
				$name = trim($name) ?: preg_replace('/@.+$/', '', $email);
				$data['receiver_nickname'][] = str_replace(',', '', $name);
			}
		}
		if ($cc = $this->message->getCc())
		{
			foreach($cc as $email => $name)
			{
				$data['receiver_email'][] = $email;
				$name = trim($name) ?: preg_replace('/@.+$/', '', $email);
				$data['receiver_nickname'][] = str_replace(',', '', $name);
			}
		}
		if ($bcc = $this->message->getBcc())
		{
			foreach($bcc as $email => $name)
			{
				$data['receiver_email'][] = $email;
				$name = trim($name) ?: preg_replace('/@.+$/', '', $email);
				$data['receiver_nickname'][] = str_replace(',', '', $name);
			}
		}
		
		$data['member_regdate'] = implode(',', array_fill(0, count($data['receiver_email']), date('YmdHis')));
		$data['receiver_email'] = implode(',', $data['receiver_email']);
		$data['receiver_nickname'] = implode(',', $data['receiver_nickname']);
		
		$url = 'https://woorimail.com/index.php';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(dirname(__FILE__)) . '/tpl/cacert/cacert.pem');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json, text/javascript, */*; q=0.1'));
		$result = curl_exec($ch);
		curl_close($ch);
		
		if ($result === false)
		{
			$this->errors = array('Woorimail: cannot connect to API server');
			return false;
		}
		
		$result = @json_decode($result, true);
		if ($result['result'] === 'OK')
		{
			return true;
		}
		elseif (isset($result['error_msg']))
		{
			if(isset(self::$_error_codes[$result['error_msg']]))
			{
				$result['error_msg'] .= ' ' . self::$_error_codes[$result['error_msg']];
			}
			$this->errors = array('Woorimail: ' . $result['error_msg']);
			return false;
		}
		else
		{
			$this->errors = array('Woorimail: server returned invalid JSON response');
			return false;
		}
	}
}
