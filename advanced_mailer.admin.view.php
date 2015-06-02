<?php

class Advanced_MailerAdminView extends Advanced_Mailer
{
	public function dispAdvanced_MailerAdminConfig()
	{
		$member_config = getModel('module')->getModuleConfig('member');
		
		Context::set('advanced_mailer_config', $this->getConfig());
		Context::set('webmaster_name', $member_config->webmaster_name ? $member_config->webmaster_name : 'webmaster');
		Context::set('webmaster_email', $member_config->webmaster_email);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('config');
	}
}
