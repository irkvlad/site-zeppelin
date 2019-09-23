<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

class zepp_client_contact extends JTable
{

	public $id = null;// @var int(11) - Первичный ключ

	public $id_client = null; // @var int(11) - Ключ соответсвия в zepp_client

	public $town = "Иркутск "; // @var varchar(30) - Город

	public $post = "Менеджер ";//@var date - должность

	public $telefon = null; // @var int(12)  - телефон

	public $email = null; // @var varchar(60) - электронный адрес

	public $fio = null; // @var varchar(60) -ФИО

	public $birthday = null; // @var date - День рождения


	/**
	 * Конструктор
	 *
	 * @param object $db Объект базы данных JDatabase
	 */
	function __construct(&$db)
	{
		parent::__construct('#__zepp_client_contact', 'id', $db);
	}
}