<?php

class Advanced_Mailer extends ModuleObject
{
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
