<?php

/**
 * @file advanced_mailer.model.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
 * @brief Advanced Mailer Model
 */
class Advanced_MailerModel extends Advanced_Mailer
{
	/**
	 * Replace the built-in Mail class with the Advanced Mailer equivalent.
	 * This method is called by the moduleHandler.init trigger.
	 */
	public function triggerReplaceMailClass()
	{
		if(class_exists('Mail', false)) return;
		if(version_compare(PHP_VERSION, '5.4', '<')) return;
		
		include_once __DIR__ . '/classes/base.class.php';
		class_alias('Advanced_Mailer\\Base', 'Mail');
		Mail::$config = $this->getConfig();
	}
}
