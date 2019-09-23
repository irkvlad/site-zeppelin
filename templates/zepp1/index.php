
<?php
/**
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
<jdoc:include type="head" />

<link rel="stylesheet"
	href="<?php echo $this->baseurl ?>/templates/system/css/system.css"
	type="text/css" />
<link rel="stylesheet"
	href="<?php echo $this->baseurl ?>/templates/system/css/general.css"
	type="text/css" />
<link rel="stylesheet"
	href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css"
	type="text/css" />


</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			<a href="index.php">
				<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/image/logo1.jpg"
					alt="дизайн студия ЦеППелин" border="0">
			</a>
		</div>
    	<div id="menu">

			<jdoc:include type="modules" name="menu" />

		</div>





	</div>
  <div id="container2"></div>
 <div id="mainContent1"></div>
  <div id="mainContent2"></div>

  <div id="mainContent3"></div>
  <div id="mainContent4">
    <p>Дизайн-студия «ЦеППелин». Офисы:<br />       г. Иркутск, ул. Лермонтова, 78, оф. 407, тел./факс: (395-2) 38-86-09, 39-22-23, e-mail: zepp@zepp.ru<br />       г. Иркутск, ул. Октябрьской Революции, 1 корпус 10, 1 этаж, тел./факс: (395-2) 48-08-83, 65-03-37, e-mail: led@zepp.ru </p>
  </div>
<!--end body--><!--start footer--><div id="footer"></div>
</div>
</body>
</html><!--end footer-->