<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">

    function volidForm(){
        var volid = adminForm.volid.value;
        var load_file = adminForm.load_file.checked;

        if( volid != 'false' && load_file ) adminForm.submit();
        else if (volid != 'false') sendFile();
        else alert('Заполните поля выделенные красным!')
    }

    function sendFile(){
        var file=document.getElementById("file");
        var xhr=new XMLHttpRequest();
        var form=new FormData();
        var sent = true;
        var name_file = document.getElementById('name_file');

        var upload_file=file.files[0];
        var spl = upload_file.name.split(".");
        var ext = spl[spl.length-1]
        form.append("file",upload_file);
        var path_name = name_file.value + "." + ext;//upload_file.name);

        var metod = "GET";
        var path = "https://cloud-api.yandex.net:443/v1/disk/resources/upload?path=disk%3A%2F%D0%9F%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D1%8F%2FWebClient%2F" + path_name + "&overwrite=true";
        var linkpath =  encodeURI( "app:/" + path_name );
        xhr.open ( metod, path, false );
        xhr.setRequestHeader('Authorization', 'OAuth AQdZ6EAAAy13esDIwC66SpaP29FTH9QyXw');
        xhr.send();


        if (xhr.status == 200) {
            //alert("тут отправляем файл");
            otvet = JSON.parse( xhr.responseText );

            var t = document.getElementById("div1");
            var metod = "PUT";
            var path = otvet.href;
            var xhr2 = new XMLHttpRequest();
            xhr2.open( metod, path , true );
            xhr2.upload.onprogress = function(event) {
                //alert( 'Загружено на сервер ' + event.loaded + ' байт из ' + event.total );
                t.innerHTML = 'Загружено на сервер ' + ( event.loaded / 1000000 ) + ' Мб из ' + ( event.total / 1000000 );
            }

            xhr2.upload.onload = function() {                
                t.innerHTML = t.innerHTML + '<br>Данные полностью загружены на сервер!';
                var xhr3 = new XMLHttpRequest();
                var metod = "GET";
                var path = "https://cloud-api.yandex.net:443/v1/disk/resources/download?path=" + linkpath;
                xhr3.open ( metod, path, false );
                xhr3.setRequestHeader('Authorization', 'OAuth AQdZ6EAAAy13esDIwC66SpaP29FTH9QyXw');
                xhr3.send();
                if (xhr3.status == 200) {
                    otvet = JSON.parse(xhr3.responseText);

                    adminForm.link.value = otvet.href;

                    alert('Данные полностью загружены на сервер!  Cылка для скачивания файла: \r\n' + otvet.href);
                    adminForm.submit();
                }else {
                    alert( 'Произошла ошибка при загрузке данных на сервер!' + xhr3.statusText + '(' + xhr3.status + ')' );
                }
                //sent = false;
                //return false;
            }

            xhr2.upload.onerror = function() {
                //alert( 'Произошла ошибка при загрузке данных на сервер!' );
                t.innerHTML = t.innerHTML + '<br>Произошла ошибка при загрузке данных на сервер!' + xhr2.statusText + '(' + xhr2.status + ')';
                //sent = false;
                //return false;
            }
            xhr2.send(form);
            //while (sent){}
        }else{
            alert( 'Произошла ошибка при загрузке данных на сервер!' + xhr.statusText + '(' + xhr.status + ')' );
        }
        //xhr.send(form);
        //alert( 'выход' );
        //return false;
    }

    function skip(){
        document.adminForm.task.value = "выход";
        adminForm.submit();
        return true;
    }

    function skleiYY(){
        var skleiX = adminForm.skleiX.checked;
        var skleiY = adminForm.skleiY.checked;
        var storonaY = adminForm.storonaY.value;
        var storonaX = adminForm.storonaX.value;

        if ( storonaY != 0 && storonaX != 0) {
            if (skleiX && skleiY)  adminForm.skley.value = (parseInt(storonaX) + parseInt(storonaY)) * 2;
            else if (skleiX) adminForm.skley.value = storonaX;
            else if (skleiY) adminForm.skley.value = storonaY;
            else adminForm.skley.value = "";
        }
    }

    function luversYX(){
        var luversX = adminForm.luversX.checked;
        var luversY = adminForm.luversY.checked;
        var storonaY = adminForm.storonaY.value;
        var storonaX = adminForm.storonaX.value;

        if ( storonaY != 0 && storonaX != 0) {
            if ( luversX && luversY )  adminForm.luvers.value = ((parseInt(storonaX) + parseInt(storonaY)) * 2) / 300;
            else if ( luversX ) adminForm.luvers.value = (parseInt(storonaX) * 2) / 300;
            else if ( luversY ) adminForm.luvers.value = (parseInt(storonaY) * 2) / 300;
            else adminForm.luvers.value = "";
        }
    }

    function changeDoc(){
        var name_file = adminForm.name_file;//document.getElementById('name_file');
        var stanok = adminForm.stanok.value;
        var vidBaner = adminForm.vidBaner.value;
        var vidPlenka = adminForm.vidPlenka.value;
        var tip = adminForm.tip.value;
        var materyal=adminForm.materyal.value;
        var skley=adminForm.skley.value;
        var polya=adminForm.polya.value;
        var luvers = adminForm.luvers.value;
        var storonaY = adminForm.storonaY.value;
        var storonaX = adminForm.storonaX.value;
        var copy = adminForm.copy.value;
        var laminacya = adminForm.laminacya.value;
        var bukvy  = adminForm.bukvy.value;
        var nomer = adminForm.nomer.value;
        var coment = adminForm.coment.value;
        var load_file = adminForm.load_file.checked;
        var file = adminForm.file.value;

        if (load_file) {// если файл уже на сервере убираем не нужные поля
			document.getElementById('alt').style.display='block';
            document.getElementById('mater').style.display='none';
            document.getElementById('numbers').style.display='none';
            document.getElementById('nomer').style.display='none';

            if (file == "") document.getElementById('files').style.border='1px solid red';
            else document.getElementById('files').style.border='1px solid black';

            if ( file == "" ) { //признак что форма прошла валидацию
                adminForm.volid.value = false;
            }else{adminForm.volid.value = true;name_file.value =file;}

        }else {// Иницилизируем поля при каждом изминении формы
			document.getElementById('alt').style.display='none';
            document.getElementById('mater').style.display='block';
            document.getElementById('numbers').style.display='block';
            document.getElementById('nomer').style.display='block';
            document.getElementById('bukvy').style.border='1px solid black';
            document.getElementById('numer').style.border='1px solid black';
            document.getElementById('storonaY').style.border='1px solid black';
            document.getElementById('storonaX').style.border='1px solid black';
            document.getElementById('files').style.border='1px solid black';
            document.getElementById('vidBaner').style.display='none';
            document.getElementById('vidPlenka').style.display='none';
            document.getElementById('white').style.display='none';
            document.getElementById('p1').style.display='block';
            document.getElementById('p2').style.display='block';
            adminForm.polya.style.display = 'block';
            adminForm.skley.style.display = 'block';
            adminForm.luvers.style.display = 'block';
            adminForm.laminacya.style.display = 'block';
            adminForm.materyal.style.display = 'block';
            adminForm.materyal2.style.display = 'none';


            if (copy < 2) copy = ""; else copy = "_" + copy; //если печать одной копии то в названии поле не нужно
            if ((storonaY > storonaX) && storonaY != 0 && storonaX != 0) { //превой должна идти меньшая сторона
                var s = storonaY;
                storonaY = storonaX;
                storonaX = s;
                adminForm.storonaY.value = storonaY;
                adminForm.storonaX.value = storonaX;
            }

            if (storonaY != 0 && storonaX != 0) { //площадь печати
                adminForm.ploschad.value = (storonaX / 1000) * (storonaY / 1000);
            }

            //Если поля не указаны убираем их из названия
            if (coment != "") coment = "_" + coment; else coment = "";
            if (laminacya != "") laminacya = "_" + laminacya; else laminacya = "";
            if (parseInt(luvers) > 0) luvers = "_л" + luvers; else luvers = "";
            if (parseInt(skley) > 0) skley = "_с" + skley; else skley = "";
            if (parseInt(polya) > 0) polya = "_п" + polya; else polya = "";

            //обязательные елементы , отмечаем если не заполнены
            if (bukvy == "") document.getElementById('bukvy').style.border = '1px solid red';
            if (parseInt(nomer) < 1) document.getElementById('numer').style.border = '1px solid red';
            if (parseInt(storonaY) < 1 || parseInt(storonaX) < 1) {
                document.getElementById('storonaY').style.border = '1px solid red';
                document.getElementById('storonaX').style.border = '1px solid red';
            }
            if (file == "") document.getElementById('files').style.border = '1px solid red';
            //ставим признак что форма не прошла валидацию
            if (parseInt(nomer) < 1 || bukvy == "" || storonaY < 1 || storonaX < 1 || file == "")  adminForm.volid.value = false;
            else adminForm.volid.value = true;

            //иницилизируем строку с именем файла
            // name_file.value = stanok + "_" + vid + tip + polya + skley + luvers + "_" + storonaY + "_" + storonaX + copy + laminacya + "_" + bukvy + "_" + nomer + coment;
            if (stanok == "Р" || stanok == "Ф") {
                //взависимости от основы материала убираем\показываем соответсвующие поля

                var boxvidBaner = document.getElementById('vidBaner');
                var boxvidPlenka = document.getElementById('vidPlenka');
                var tipbox = document.getElementById('tip');

                if (materyal == 'Пленка') {
                    boxvidPlenka.style.display = 'block';
                    boxvidBaner.style.display = 'none';
                    materyal = materyal + "_" + vidPlenka + tip;
                } else if (materyal == 'Банер') {
                    boxvidPlenka.style.display = 'none';
                    boxvidBaner.style.display = 'block';
                    materyal = materyal + "_" + vidBaner;
                } else {
                    boxvidPlenka.style.display = 'none';
                    boxvidBaner.style.display = 'none';

                }
            }else if(stanok == "УФ") {
                document.getElementById('vidBaner').style.display = 'none';
                document.getElementById('vidPlenka').style.display = 'none';
                document.getElementById('white').style.display='block';
                document.getElementById('p1').style.display='none';
                document.getElementById('p2').style.display='none';
                adminForm.materyal.style.display = 'none';
                adminForm.polya.style.display = 'none';
                adminForm.skley.style.display = 'none';
                adminForm.luvers.style.display = 'none';
                adminForm.laminacya.style.display = 'none';
                adminForm.materyal2.style.display = 'block';
                polya=materyal = "";
                if (adminForm.white.checked) materyal = "белый_";
                materyal = materyal + adminForm.materyal2.value + "_"+ tip;
            }
            else if(stanok == "Ламинация") {
                polya=materyal = "";
                document.getElementById('vidBaner').style.display = 'none';
                document.getElementById('vidPlenka').style.display = 'none';
                document.getElementById('p1').style.display='none';
                document.getElementById('p2').style.display='none';
                adminForm.materyal.style.display = 'none';
                adminForm.polya.style.display = 'none';
                adminForm.skley.style.display = 'none';
                adminForm.luvers.style.display = 'none';
                adminForm.laminacya.style.display = 'none';
                materyal = "_"+ tip;
            }

            name_file.value = stanok + "_" + materyal + polya + skley + luvers + "_" + storonaY + "_" + storonaX + copy + laminacya + "_" + bukvy + "_" + nomer + coment;
        }

    }

</script>

<form action="index.php?option=com_zepp_polnocvet&view=main"
      onsubmit="" method="post" name="adminForm" onchange = changeDoc();>

    <div id="toolbar-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="toolbar" class="toolbar">                
                    <input type="button" onclick="skip();" name="send" class="button" value="Выход" />
                <?php if ( ($this->user['usergid'] >= 23) ) : ?>
                    <input type="button" onclick="volidForm();" name="send" class="button" value="Отправить" />
                <?php endif; ?>
            </div>
            <div  class="header" > Выберите файл для печати </div>
            <div class="clr"></div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
        <div class="clr"></div>

    </div>
    <div id="element-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div class="checkboxs">
                <p>Файл уже на сервере <input type="checkbox" name="load_file" checked > ДА </p>
				<p id="alt" style=""><b style="color:red;">файл не будет загружаться на сервер!</b></p>
                <p><b>Название файла: </b><b  ></b><input id = "name_file" type="text" name= "name_file" size="140"></p>
                <div class="clr" style="border: none;display: none;" ></div>
                <div style=" " id="mater">
                    <p><b>Укажите станок:</b></p>
                    <p><select size="1"  name="stanok" onchange = changeStanok(); >
                        <option selected value="Р">Роланд</option>
                        <option  value="Ф">Феникс</option>
                        <option  value="УФ">УФ</option>
                        <option  value="Ламинация">Ламинация</option>
                    </select></p>

                    <p><b>Укажите материал:</b></p>
                    <p><select size="1"  name="materyal"  >
                            <option selected value="Пленка">Пленка</option>
                            <option  value="Банер">Банер</option>
                            <option  value="Полистер">Полистер</option>
                            <option  value="Перфорированная_пленка">Перфорированная пленка</option>
                            <option  value="Обои">Обои</option>
                            <option value="Пентопринт_молочный">Пентопринт молочный</option>
                            <option value="Пентопринт_прозрачный">Пентопринт прозрачный</option>
                            <option value="Пентопринт_пластик">Пентопринт пластик</option>
                            <option value="Фотобумага">Фотобумага</option>
                            <option value="Постерная_бумага">Постерная бумага</option>
                        </select>
                        <input type="text" name= "materyal2" size="40" VALUE="Материал_заказчика">
                    </p>
                    <p id="white">Белый цвет <input type="checkbox" name="white" ></p>

                    <div style="display:none;" id="vidBaner">
                        <p style="padding: 0"><b>Основа:</b></p>
                        <p><select size="1"  name="vidBaner"  >
                                <option selected value="300">300</option>
                                <option value="440">440</option>
                                <option value="510">510</option>
                            </select></p>
                    </div>
                    <div style="display:none;" id="vidPlenka">
                        <p style="padding: 0"><b>Основа:</b></p>
                        <p><select size="1"  name="vidPlenka"  >
                                <option selected value="010">010</option>
                                <option value="000">000</option>
                            </select></p>
                    </div>
                    <p><select size="1" id="" name="tip" style="padding: 0">
                            <option selected value="г">Глянцевая</option>
                            <option value="м">Матовая</option>
                        </select></p>
                </div>

                <div style="" id='numbers'>
                    <p><b>Размер полей в мм:</b></p>
                    <p><input type="number" size="3" name="polya" step="10" value="10"></p>
                    <p><b>Меньшая сторона изображения:</b></p>
                    <p><input id="storonaY" type="number" size="7" name="storonaY" value="0">
                        Склей <input type="checkbox" name="skleiY" onclick="skleiYY();" >
                        Люверсы <input type="checkbox" name="luversY" onclick="luversYX();" ></p>
                    </p>
                    <p><b>Большая сторона изображения:</b></p>
                    <p><input id="storonaX" type="number" size="7" name="storonaX" value="0">
                        Склей <input type="checkbox" name="skleiX" onclick="skleiYY();">
                        Люверсы <input type="checkbox" name="luversX" onclick="luversYX();" ></p>
                    </p>
                    <p id="p1"><b>Количество склея в п.м. НЕ ЗАБУДЬ про ПЕРИМЕТР:</b></p>
                    <p><input type="number" size="3" name="skley" value="0"></p>
                    <p id="p2"><b>Количество люверсов ЛЮВЕРСЫ РИСУЕМ:</b></p>
                    <p><input type="number" size="3" name="luvers" value="0"></p>
                    <p><b>Количество копий</b></p>
                    <p><input type="number" size="3" name="copy" value="1"></p>
                    <p><b>Тип материала ламинации</b></p>
                    <p><select size="1"  name="laminacya"  >
                            <option selected value="">Без ламинации</option>
                            <option value="000м">000м</option>
                            <option value="000г">000г</option>
                         <!--   <option value="010м">010м</option>
                            <option value="010г">010г</option> -->
                        </select></p>
                </div>


                <div style="" id='nomer'>
                    <p><b>Инициалы:</b><br>
                        <input id="bukvy" type="text" name= "bukvy" size="2" value="<?php echo $this->user['pr_user']; ?>">
                    <p><b>Номер:</b><br>
                        <input id="numer" type="number" name= "nomer" size="6" value="0">
                    <p><b>Коментарий:</b><br>
                        <input type="text" name= "coment" size="40" VALUE="">
                </div>

                <div style="" id='files'>
                    <p><b>Выберите файл</b><br>
                        <input type="file" multiple name="file[]" id="file">
                    <div id="div1" style="border: none;"> </div>
                </div>


            </div><div class="clr"></div></div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
    </div>
    <input type="hidden" name="link" value="" />
    <input type="hidden" name="volid" value="false" />
    <input type="hidden" name="task" value="save_record" />
    <input type="hidden" name="ploschad" value="" />
    <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
</form>
<?php //if ($this->userGroup['group_id'] == 10) : ?>
<!--
<h3>Кнопки ниже не работают</h3>

<input type="submit" onclick="" name="no" class="button" value="Указать проект" />
-->
<?php //endif; ?>

<script type="text/javascript">
    changeDoc();
</script>
