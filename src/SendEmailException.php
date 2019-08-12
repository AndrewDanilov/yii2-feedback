<?php
namespace andrewdanilov\feedback;

class SendEmailException extends \LogicException
{
	public function __construct()
	{
		parent::__construct('Возникла ошибка на сервере при отправке сообщения');
	}
}