<?php

/**
 * @file xternal_mailer.addon.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
 * @brief Xternal Mailer addon
 */

if(!defined('__XE__')) exit;
if($called_position !== 'before_module_init') return;
if(version_compare(PHP_VERSION, '5.3', '<')) return;
if(class_exists('Mail', false)) return;

switch($addon_info->send_type)
{
	case 'mail':
	case 'smtp':
	case 'ses':
	case 'mailgun':
	case 'mandrill':
	case 'sendgrid':
	case 'woorimail':
		include_once __DIR__ . '/vendor/autoload.php';
		include_once __DIR__ . '/classes/base.class.php';
		include_once __DIR__ . '/classes/' . $addon_info->send_type . '.class.php';
		class_alias('Xternal_Mailer_' . ucfirst($addon_info->send_type), 'Mail');
		Xternal_Mailer_Base::$config = $addon_info;
		return;
		
	default:
		return;
}
