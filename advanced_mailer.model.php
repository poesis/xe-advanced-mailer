<?php

class Advanced_MailerModel extends Advanced_Mailer
{
	public function triggerReplaceMailClass()
	{
		if(class_exists('Mail', false)) return;
		
		include_once __DIR__ . '/classes/base.class.php';
		class_alias('Advanced_Mailer\\Base', 'Mail');
		Mail::$config = $this->getConfig();
	}
}
