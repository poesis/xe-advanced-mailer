<?php

/**
 * @file advanced_mailer.model.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
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
		$config = $this->getConfig();
		if($config->is_enabled === 'N') return;
		if(class_exists('Mail', false)) return;
		if(version_compare(PHP_VERSION, '5.3', '<')) return;
		if(defined('RX_VERSION')) return;
		
		include_once __DIR__ . '/classes/base.class.php';
		class_alias('Advanced_Mailer\\Base', 'Mail');
		Mail::$config = $config;
	}
}
