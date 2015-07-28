<?php

namespace Advanced_Mailer;

/**
 * @file dummy.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license LGPL v2.1 <http://www.gnu.org/licenses/lgpl-2.1.html>
 * @brief Advanced Mailer Transport: Dummy
 */
class Dummy extends Base
{
	public $assembleMessage = false;
	
	public function send()
	{
		return true;
	}
}
