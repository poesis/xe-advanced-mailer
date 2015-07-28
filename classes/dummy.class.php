<?php

namespace Advanced_Mailer;

/**
 * @file dummy.class.php
 * @author Kijin Sung <kijin@kijinsung.com>
 * @license GPLv2 or Later <https://www.gnu.org/licenses/gpl-2.0.html>
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
