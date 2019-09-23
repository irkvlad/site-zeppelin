<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

class TablePortfolios extends JTable
{

	public $id = null;// @var integer - Первичный ключ

	public $logo_path = "../images/porfolio/no_user.png" 	; // @var text - Путь к фотографии автара

	public $fio = null; // @var string - Фамилия Имя Отчество

	public $date_rojjdeniy = null;//@var date - Дата рождения

	public $telefon = null; // @var int - Телефон

	public $email = null; // @var strin - email

	public $student = null; // @var string - Место учебы

	public $worcked = null; // @var string - Место работы

	public $photo_path =null; // @var string - Портфолио путь к фотографиям

	public $date_reg = 0; // @var timestamp - Дата занесения в базу

	public $notes = null; // @var string- Заметки

    public $privut_notes = null;

	//public $user_id = null;//@var integer - Кто внес в базу

	//public $agroup = 0; // @var int - Группа, портфолио

	//Навыки :

	public $thri_d = 0; // @var bool

	public $photoshop  = 0; // @var bool  -

	public $corel = 0; // @var bool  -

	public $auto_cad = 0; // @var bool  -

	public $web_disign = 0; // @var bool  -

	//Рейтинг
	public $star_reyting = 0; // @var bool  -



	/**
	 * Конструктор
	 *
	 * @param object $db Объект базы данных JDatabase
	 */
	function __construct(&$db)
	{
		parent::__construct('#__portfolios', 'id', $db);
	}
}