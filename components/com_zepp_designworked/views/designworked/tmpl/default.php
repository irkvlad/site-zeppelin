<?php defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
	<!--
	function validate_form_add() { document.adminForm.task.value = "add"; return true;   }
	function validate_form_remove() { 
		if (confirm("Удалить?")) {
			document.adminForm.task.value = "remove"; 
			return true;   
			}else return false
	}
	function validate_form_cancel() { document.adminForm.task.value = "cancel"; return true;   }

	function validate_form() { 
		if (document.adminForm.task.value == "") return false;  
		return true;
		}
	//-->
</script>
<table width="100%">
	<tr><td colspan="2" valign="top">
		<div id="toolbar-box">
			<div class="t"><div class="t"><div class="t"></div></div></div>
			<div class="m">
				<div id="toolbar" class="toolbar">
					<form action="index.php" method="post" name="adminForm" onsubmit="return validate_form();">
						<?php //if ( ($this->user->gid > 23)  ) : ?>
							<input style="float:left;" type="submit" onclick="validate_form_add();" name="submit_add" class="button" value="Править" />
						<?php //endif; ?>
							<input style="float:left;" type="submit" onclick="validate_form_remove();" name="submit_remove" class="button" value="Удалить(все)" />
							<input style="float:left;" type="submit" onclick="validate_form_cancel();" name="submit_cancel" class="button" value="Назад" />
						
						<input type="hidden" name="option" value="com_zepp_designworked" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
						<input type="hidden" name="view" value="designworked" />
						<input type="hidden" name="controller" value="designworked" />
						<input type="hidden" name="cid" value="<?php echo $this->data->id ?>" />
						<input type="hidden" name="dis" value="<?php echo $this->dis; ?>" />
						<input type="hidden" name="cat" value="<?php echo $this->cat; ?>" />
					</form>
				</div>
				<div  class="header" >Галерея работ</div>
				<div class="clr"></div>
			</div>
			<div class="b"><div class="b"><div class="b"></div></div></div>
			<div class="clr"></div>

		</div>
	</tr></td>
	<tr><td valign="top">
			<div id="element-box">
				<div class="t"><div class="t"><div class="t"></div></div></div>
				<div class="m">
					<?php
					echo '<ul>';
						foreach( $this->item as $item){
							echo '<li class="ds_wkr menu">';
							//http://zepp/index.php?option=com_zepp_designworked&view=designworked&dis=105&cat=1&Itemid=107
								echo '<a href="index.php?option=com_zepp_designworked&view=designworked&dis='.$this->dis.'&cat='.$this->cat.'&cid='.$item->id.'">'.$item->name.'</a>';
							echo '</li>';
						}
					echo '</ul>';
					?>
				</div>
				<div class="b"><div class="b"><div class="b"></div></div></div>
			</div>
		</td>
		<td valign="top">
			<div id="element-box">
				<div class="t"><div class="t"><div class="t"></div></div></div>
				<div class="m">
					<h1><?php echo $this->cat_name ?></h1>
					<h2>Название: <?php echo $this->data->name; ?>"</h2>
					<p><?php echo $this->data->coment; ?></p>

					<div>
					<?php
					foreach($this->images as $image){
						echo 	'<div style="float: left;"><a style="
									margin: 5px;
									display: block;
									float: left;
									max-width: 180px;
									min-width: 180px;
									max-height: 140px;
									min-height: 140px;
									overflow: hidden;
									text-align: center;
							" target="_blank" href="'.$image['path'].'" ><div>'.$image['privu'].'<br>'.$image['alt'].'</div></a></div> ' ;
					}
					?>
					</div>
					<div class="clr"></div>

					<h3><?php echo $this->designer_name; ?></h3><br>
				</div>
				<div class="b"><div class="b"><div class="b"></div></div></div>
			</div>
		</td></tr>
</table>
<!--
<form action="index.php" enctype="multipart/form-data" method="POST" name="adminForm" id="adminForm">


	<input type="hidden" name="option" value="com_zepp_designworked" />
	<input type="hidden" name="controller" value="designworked" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>-->

<?php //dump($this); ?>
