<?php

class Advanced_MailerModel extends Advanced_Mailer
{
	public function triggerReplaceMailClass()
	{
		if(class_exists('Mail', false)) return;
		
		$config = $this->getConfig();
		
		include_once __DIR__ . '/vendor/autoload.php';
		include_once __DIR__ . '/classes/base.class.php';
		include_once __DIR__ . '/classes/' . $config->send_type . '.class.php';
		class_alias('Advanced_Mailer\\' . ucfirst($config->send_type), 'Mail');
		Mail::$config = $config;
	}
}
