<?php defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser();
$linkM1 = '<button class="button" onclick="document.location.assign( \'index.php?option=com_zepp_ringclient&view=ringclient&task=getzakaz&layout=form&Itemid='.$this->itemid.'&id=';
//                                         http://zepp/index.php?option=com_zepp_ringclient&view=ringclient&layout=form
$linkM2= ' \')" >Мое</button>  ';

$linkA1 = '<button class="button" onclick="document.location.assign( \'index.php?option=com_zepp_ringclient&view=ringclient&layout=form&Itemid='.$this->itemid.'&id=';//
$linkA2= ' \')" >Править</button>  ';
?>
<div id="toolbar-box">
    <div class="t"><div class="t"><div class="t"></div></div></div>
    <div class="m">
        <div id="toolbar" class="toolbar">
            <form action="index.php" method="post" name="adminForm">
                <input type="hidden" name="option" value="zepp_ringclient" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
                <!--<input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="controller" value="ringclient" />-->
            </form>
        </div>
        <div  class="header" >Новые заказы </div>
        <div class="clr"></div>        
    </div>
    <div class="b"><div class="b"><div class="b"></div></div></div>
    <div class="clr"></div>
    
</div>
<div id="element-box">
    <div class="t"><div class="t"><div class="t"></div></div></div>
    <div class="m">
        
            <table class="adminlist">
            <thead>
                <tr>
                    <th width="5"><?php echo JText::_( '№' ); ?></th>					
                    <th><?php echo JText::_( 'Дата' ); ?></th>
                    <th><?php echo JText::_( 'Тема' ); ?></th>
                    <th><?php echo JText::_( 'Клиент' ); ?></th>
                    <?php if ($user->gid > 23) : ?>
                        <th><?php echo JText::_( 'Телефон' ); ?></th>
                        <th><?php echo JText::_( 'Создал' ); ?></th>
                    <?php endif; ?>
                    <?php if ( ($this->userGroup['group_id'] == 10) OR ($user->gid > 23) ) : ?>
                        <th><?php echo JText::_( 'Действие' ); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <?php
            $k = 0;
            for ($i=0, $n=count( $this->Data ); $i < $n; $i++)	{
                    $row = &$this->Data[$i];
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $i+1 ; //$row->id; ?></td>
                        <?php
                        $date = JFactory::getDate( $row->creator_date);
                        $datestr = $date->toFormat ('(%H:%M)%d.%m.%Y');                         
                        ?>
                        <td><?php echo $datestr; ?></td>
                        <td><?php echo $row->tema; ?></td>
                        <td><?php echo $row->client; ?></td>
                        <!-- Для админов -->
                        <?php if ($user->gid > 23) : ?>
                            <td><?php echo $row->telefon; ?></td>
                            <td><?php echo $row->creator_name; ?></td>
                            <td><?php echo $linkA1.$row->id.$linkA2; ?></td>
                        <?php endif; ?>
                        <!-- Для менеджеров -->    
                        <?php if ($this->userGroup['group_id'] == 10) : ?>
                            <td><?php echo $linkM1.$row->id.$linkM2; ?></td>
                        <?php endif; ?>

                    </tr>
                    <?php
                    $k = 1 - $k;
            }
            ?>
            </table>
        
    </div>
    <div class="b"><div class="b"><div class="b"></div></div></div>
</div>

