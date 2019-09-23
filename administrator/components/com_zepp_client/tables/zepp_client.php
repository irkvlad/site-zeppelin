<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

class zepp_client extends JTable
{

	public $id = null;// @var int(11) - Первичный ключ

	public $modifer_time = null; // @var timestamp - дата изминения

	public $modifer_user = null; // @var int(11) - кто изменил

	public $date_rojjdeniy = null;//@var date - Дата рождения

	public $on_start = null; // @var date  - дата начала сотрудничества

	public $cast = 0; // @var decimal(10,0) - Общая сумма по проектам

	public $likes = 0; // @var int(11) - Лояльность к нам

	public $on_send = null; // @var date - дата напоминания

	public $name ="NO_BADY"; // @var varchar(60) - Название организации( бренд)

	public $legal_entity = null; // @var timestamp - Юр. Лицо
	public $send = null;

	/**
	 * Конструктор
	 *
	 * @param object $db Объект базы данных JDatabase
	 */
	function __construct(&$db)
	{
		parent::__construct('#__zepp_client', 'id', $db);
	}
}