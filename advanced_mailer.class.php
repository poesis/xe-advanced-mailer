<?php

class Advanced_Mailer extends ModuleObject
{
	public function getConfig()
	{
		$config = getModel('module')->getModuleConfig('advanced_mailer');
		if(!is_object($config) || !isset($config->send_type))
		{
			$config = new stdClass();
			$config->send_type = 'mail';
		}
		return $config;
	}
	
	public function moduleInstall()
	{
		$oModuleController = getController('module');
		$oModuleController->insertTrigger('moduleHandler.init', 'advanced_mailer', 'model', 'triggerReplaceMailClass', 'before');
		return new Object();
	}
	
	public function checkUpdate()
	{
		return false;
	}
	
	public function moduleUpdate()
	{
		return new Object(0, 'success_updated');
	}
	
	public function recompileCache()
	{
		// no-op
	}
}
