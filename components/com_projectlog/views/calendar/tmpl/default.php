<?php

/**
 *    Вид календарика
 */

defined('_JEXEC') or die('Restricted access');

$id = JRequest::getVar('id');

$style = 'body#bd.FF {background: #fff;color: #000;}';
$this->document->addStyleDeclaration($style, 'text/css');

JHTML::_('script', $filename = 'jquery.js', $path = 'media/system/js/jquery/', $mootools = true);
JHTML::_('script', $filename = 'ifx.js', $path = 'media/system/js/jquery/', $mootools = true);
JHTML::_('script', $filename = 'idrop.js', $path = 'media/system/js/jquery/', $mootools = true);
JHTML::_('script', $filename = 'idrag.js', $path = 'media/system/js/jquery/', $mootools = true);
JHTML::_('script', $filename = 'iutil.js', $path = 'media/system/js/jquery/', $mootools = true);
JHTML::_('script', $filename = 'islider.js', $path = 'media/system/js/jquery/', $mootools = true);
JHTML::_('script', $filename = 'color-picker.js', $path = 'media/system/js/jquery/color-picker/', $mootools = true);
JHTML::_('stylesheet', $filename = 'color-picker.css', $path = 'media/system/js/jquery/color-picker/');
$technicians = strtok(projectlogHTML::getusername($this->project->technicians), " ");
$shot_title  = $this->project->shot_title;
if ($shot_title == '') $shot_title = strtok($this->project->title, ' ');


?>

<script type="text/javascript">
    function myokfunc() {
        //alert("Пользовательский текст показываемый, после установки цвета");
    }
    //init colorpicker:
    $(document).ready(
        function () {
            $.ColorPicker.init();
        }
    );
</script>
<?php
if ($day <> '')
{
	echo '<div class="projekt_d">';
} ?>
<form action="index.php" method="post" name="adminForm" id="adminForm" onsubmit="return checkForm();">

    <table style="float:left;top:0;left:0;border: 1px dotted #CDCDCD;width:20.1cm;height:28.5cm;background-color: #fff;"
           cellpadding="0" cellspacing="0">
        <tr>
            <td style="border: 1px dotted #E9E9E9;" width="33%" height="33%">
                <div class="win_drag pointer">

					<?php
					$path = 'media/com_projectlog/docs/' . $this->logo->project_id . '/' . $this->logo->path;
					if (file_exists('media' . DS . 'com_projectlog' . DS . 'docs' . DS . $this->logo->project_id . DS . '227x219_' . $this->logo->path))
						$path = 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $this->logo->project_id . DS . '227x219_' . $this->logo->path;

					echo projectlogHTML::kalendarik(
						'227',
						$shot_title,
						$this->project->release_date,
						$this->project->job_id,
						$this->project->release_id,
						$technicians,
						$this->project->projecttype,
						$this->project->workorder_id,
						$path,//= 'media/com_projectlog/docs/'.$this->logo->project_id.'/'.$this->logo->path,
						$w = '219',
						$h = '227', $this->project->podrydchik);

					?>

                </div>

            </td>
            <td style="border: 1px dotted #CDCDCD;"></td>
            <td style="border: 1px dotted #CDCDCD;"></td>
        </tr>
        <tr>
            <td style="border: 1px dotted #CDCDCD;"></td>
            <td style="border: 1px dotted #CDCDCD;"></td>
            <td style="border: 1px dotted #CDCDCD;"></td>
        </tr>
        <tr>
            <td style="border: 1px dotted #CDCDCD;"></td>
            <td style="border: 1px dotted #CDCDCD;"></td>
            <td style="border: 1px dotted #CDCDCD;"></td>
        </tr>
        </tale>


		<?php if ($this->user->id == $this->project->manager)
		{ ?>
            <table align="center">
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="key"><?php echo JText::_('WORKORDER NUM'); ?><br/>
                        <a href="javascript:void(0);"
                           rel="colorpicker&objcode=myhexcode&objshow=myshowcolor&showrgb=1&okfunc=myokfunc"
                           style="text-decoration:none">
                            <div id="myshowcolor"
                                 style="width:15px;height:15px;border:1px solid black;float: left;background-color: #<?php echo $this->project->workorder_id; ?>;">
                                &nbsp;
                            </div>
                        </a>

                        <input type="text" class="inputbox" name="workorder_id" id="myhexcode"
                               value="<?php echo $this->project->workorder_id; ?>" style="width:60px;"/>
                    </td>
                </tr>

                <tr>
                    <td class="key"><?php echo JText::_('PROJECT TYPE'); ?><br/>

                        <a href="javascript:void(0);"
                           rel="colorpicker&objcode=myhexcode2&objshow=myshowcolor2&showrgb=1&okfunc=myokfunc"
                           style="text-decoration:none">
                            <div id="myshowcolor2"
                                 style="width:15px;height:15px;border:1px solid black;float: left;background-color: #<?php echo $this->project->projecttype; ?>;">
                                &nbsp;
                            </div>
                        </a>

                        <input type="text" class="inputbox" name="projecttype" id="myhexcode2"
                               value="<?php echo $this->project->projecttype; ?>" style="width:60px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>

                    </td>

                </tr>
            </table>
			<?php
		} ?>

		<?php echo JHTML::_('form.token'); ?>
        <input type="hidden" name="option" value="com_projectlog"/>
        <input type="hidden" name="id" value="<?php echo $this->project->id; ?>"/>
        <input type="hidden" name="view" value="calendar"/>
        <input type="hidden" name="task" value="saveCalendar"/>
        <input type="hidden" name="Itemid" value="61"/>
        <input type="hidden" name="category" value="<?php echo $this->project->category; ?>"/>
        <!--index.php?option=com_projectlog&view=calendar&id='.$p->id.'&Itemid=61'-->
</form>


<?php
if ($day <> '')
{
	echo '</div>';
} ?>

<script type="text/javascript">
    /*****************************Двганье объектов по экрану*****************************************/
    var dragMaster1 = (
        function () {
            var dragObject
            var mouseOffset

            function getMouseOffset(target, e) {
                var docPos = getPosition(target)
                return {x: e.pageX - docPos.x, y: e.pageY - docPos.y}
            }

            function mouseUp() {
                dragObject = null
                // clear events
                document.onmousemove = null
                document.onmouseup = null
                document.ondragstart = null
                document.body.onselectstart = null
            }

            function mouseMove(e) {
                e = fixEvent(e)
                with (dragObject.style) {
                    position = 'absolute'
                    top = e.pageY - mouseOffset.y + 'px'
                    left = e.pageX - mouseOffset.x + 'px'
                }
                return false
            }

            function mouseDown(e) {
                e = fixEvent(e)
                if (e.which != 1) return
                dragObject = this
                mouseOffset = getMouseOffset(this, e)
                document.onmousemove = mouseMove
                document.onmouseup = mouseUp
                // отменить перенос и выделение текста при клике на тексте
                document.ondragstart = function () {
                    return false
                }
                document.body.onselectstart = function () {
                    return false
                }
                return false
            }

            return {
                makeDraggable: function (element) {
                    element.onmousedown = mouseDown
                }
            }
        }()
    )

    function getPosition(e) {
        var left = 0
        var top = 0
        while (e.offsetParent) {
            left += e.offsetLeft
            top += e.offsetTop
            e = e.offsetParent
        }
        left += e.offsetLeft
        top += e.offsetTop
        return {x: left, y: top}
    }

    function fixEvent(e) {
        // получить объект событие для IE
        e = e || window.event

        // добавить pageX/pageY для IE
        if (e.pageX == null && e.clientX != null) {
            var html = document.documentElement
            var body = document.body
            e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
            e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
        }

        // добавить which для IE
        if (!e.which && e.button) {
            e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
        }

        return e
    }

    //$(document).ready(function() {
    //var dragObjects = document.getElementById('dragObjects1').getElementsByTagName('img');
    var dragObjects = document.getElementsByClassName('win_drag');
    for (var i = 0; i < dragObjects.length; i++) {
        dragMaster1.makeDraggable(dragObjects[i]);
    }
    //})
</script>