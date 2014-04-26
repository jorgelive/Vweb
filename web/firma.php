<?php
if (!isset($_POST['nombre'])){
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <style type="text/css">
            body { text-align: left; font-family: calibri; font-size: 11px; line-height: 11px;}
            a { font-family: calibri; text-decoration: none; border: 0;}
            a:hover { text-decoration: underline; }
            p { font-size: 14px; text-align: justify; text-decoration: none }
            .plomo{ color:#474747;}
            .amarillo{ color:#FC0; }
            .verde{ color:#060;}
        </style>
    </head>
    <body>

    <form action="firma.php" method="POST">
        Nombre: <input type="text" name="nombre"><br>
        E-mail: <input type="text" name="email"><br>
        Cargo: <input type="text" name="cargo"><br>
        Anexo: <input type="text" name="anexo"><br>
        Opcional: <input type="text" name="opcional"><br>
        Oficina: <select name="oficina"><option value="reducto">Reducto</option><option value="lamar">La Mar</option><option value="cusco">Cusco</option><option value="aqp">AQP</option></select><br>
        Idioma: <select name="idioma"><option value="es">Español</option><option value="en">Inglés</option><option value="pt">Portugués</option></select><br><br>
        <input type="submit">
    </form>

    </body>
</html>
<?php

}
else{

    function acentos($text) {
        $reemplazo=array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&uuml;","&ntilde;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Uuml;","&Ntilde;");
        $original = array("á","é","í","ó","ú","ü","ñ","Á","É","Í","Ó","Ú","Ü","Ñ");
        $text = htmlentities(str_replace($original, $reemplazo, $text));
        return $text;
    }

    $local['reducto']= "Av. Paseo de la República 6010 - Piso 7 - Lima 18, Perú";
    $local['lamar']="Av. la Mar 163 - Lima 18, Perú";
    $local['cusco']="Av. El Sol 817 - Cusco, Perú";
    $local['aqp']="Calle Palacio Viejo 216 Of. 101 Cercado - Arequipa, Perú";
    $telefono['reducto']="+51 1 610 1900";
    $telefono['lamar']="+51 1 610 1900";
    $telefono['cusco']="+51 84 221744";
    $telefono['aqp']="+51 54 612267";
    $ambiental['es']="Piensa en el planeta antes de imprimir. Reduce. Reusa. Recicla";
    $ambiental['en']="Think before printing. Reduce. Reuse. Recycle.";
    $ambiental['pt']="Pense no planeta antes de imprimir. Reduzir. Reutilizar. Reciclar.";
    $frase['es']="frase_esp.jpg";
    $frase['en']="frase_eng.jpg";
    $frase['pt']="frase_por.jpg";
    $publi['es']="publi_esp.png";
    $publi['en']="publi_eng.png";
    $publi['pt']="publi_por.png";
    $siguenos['es']="siguenos.png";
    $siguenos['en']="follow.png";
    $siguenos['pt']="siguenos_por.png";
?>


<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name=GENERATOR content="MSHTML 8.00.6001.19170">
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <style type="text/css">

        *{margin: 0; padding: 0; border: 0; }
        body { text-align: left; font-family: calibri; font-size: 11px; line-height: 11px;}
        table, tr, td {padding: 0; border: 0; border-collapse: collapse; }
        a { margin: 0; padding: 0; font-family: calibri; text-decoration: none; border: 0;}
        a:hover { text-decoration: underline; }
        p { color:#474747; margin: 0; padding: 0; font-size: 14px; text-align: justify; text-decoration: none }
        .plomo{ color:#474747;}
        .amarillo{ color:#FC0; }
        .verde{ color:#060;}
        textarea.codigo{ border:#000000 solid 1px; margin-left:10px; width:600px; height: 1000px;}
    </style>
</head>

<body class="plomo" >
<table style="width: 613px;">
    <tr>
        <td colspan="2">
            <table style="width:100%;">
                <tr>
                    <td style="width:240px;">
                        <p style="font-size:16px; font-weight:bold;">
                            <?php echo $_POST['nombre'];?>
                        </p>
                        <p class="amarillo" style="font-size:14px; font-weight:bold;">
                            <?php echo $_POST['cargo'];?>
                        </p>
                        <p style="text-decoration:underline; font-size:15px;">
                            <?php echo $_POST['email'];?>
                        </p>
                    </td>
                    <td style="width:342px;">
                        <p style="font-size:14px; text-align:right; margin-top: 9px; <?php if (!isset($_POST['opcional'])&& empty($_POST['opcional'])){ echo "margin-bottom: 10px;";}?>">
                            T. <?php echo $telefono[$_POST['oficina']];?>  <?php if (isset($_POST['anexo'])&& !empty($_POST['anexo'])){echo "Ext. ".$_POST['anexo'];}?>
                        </p>

                        <p style="font-size:13px; text-align:right; <?php if (!isset($_POST['opcional'])&& empty($_POST['opcional'])){ echo "margin-bottom: 9px;";}?>">
                            <?php echo $local[$_POST['oficina']];?>
                        </p>
                        <?php
                        if (isset($_POST['opcional'])&&!empty($_POST['opcional'])){
                        ?>

                            <p style="font-size:13px; text-align:right;">
                                <?php echo $_POST['opcional'];?>
                            </p>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="height:10px; line-height: 10px;" colspan="2">
            <img src="http://vipac.pe/firmas/2014/blank.gif" style="width:100%; height: 1px;"/>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <a href="http://vipac.pe/firmas/2014/frase.php?idioma=<?php echo $_POST['idioma'];?>" target="_blank"><img src="http://vipac.pe/firmas/2014/<?php echo $frase[$_POST['idioma']];?>" style="width:613px; height: 87px;"/></a>
        </td>
    </tr>
    <tr>
        <td style="height:5px; line-height: 5px;" colspan="2">
            <img src="http://vipac.pe/firmas/2014/blank.gif" style="width:496px; height: 1px;"/>
        </td>
    </tr>
    <tr>
        <td align="center" style="width:151px; margin:0px; background: #000">
            <table style="width:133px; height: 39px;">
                <tr>
                    <td>
                        <img src="http://vipac.pe/firmas/2014/<?php echo $siguenos[$_POST['idioma']];?>" style="width:58px; height: 40px;" />
                    </td>
                    <td>
                        <a href="https://www.facebook.com/vipacperu">
                            <img src="http://vipac.pe/firmas/2014/face.png" style="width:18px; height: 19px;" />
                        </a>
                    </td>
                    <td>
                        <a href="http://blog.vipac.pe/" target="_blank">
                            <img src="http://vipac.pe/firmas/2014/blog.png" style="width:18px; height: 19px;" />
                        </a>
                    </td>
                </tr>

            </table>
        </td>
        <td style="width:465px; margin:0px;">
            <a href="http://vipac.pe/firmas/2014/publi.php?idioma=<?php echo $_POST['idioma'];?>" target="_blank"><img src="http://vipac.pe/firmas/2014/publi/<?php echo $publi[$_POST['idioma']];?>" style="width:465px; height:70px;" /></a>
        </td>
    </tr>
    <tr>
        <td style="height:5px; line-height: 5px;" colspan="2">
            <img src="http://vipac.pe/firmas/2014/blank.gif" style="width:100%; height:1px;" />
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center" bgcolor="#FFFFFF">
            <p class="verde" style="font-size:13px;"><img src="http://vipac.pe/firmas/2014/hoja.png" style="width:16px; height:15px;" /> <?php echo $ambiental[$_POST['idioma']];?></p>
        </td>
    </tr>
</table>
<br>
<br>
<br>
    <textarea class="codigo">
        <html lang="es">
        <head>
            <meta charset="utf-8"/>
            <meta name=GENERATOR content="MSHTML 8.00.6001.19170">
            <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
            <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
            <style type="text/css">

                *{margin: 0; padding: 0; border: 0; }
                body { text-align: left; font-family: calibri; font-size: 11px; line-height: 11px;}
                table, tr, td {padding: 0; border: 0; border-collapse: collapse; }
                a { margin: 0; padding: 0; font-family: calibri; text-decoration: none; border: 0;}
                a:hover { text-decoration: underline; }
                p { color:#474747; margin: 0; padding: 0; font-size: 14px; text-align: justify; text-decoration: none }
                .plomo{ color:#474747;}
                .amarillo{ color:#FC0; }
                .verde{ color:#060;}
            </style>
        </head>

        <body class="plomo">

        <table style="width: 613px;">
            <tr>
                <td colspan="2">
                    <table style="width:100%;">
                        <tr>
                            <td style="width:240px;">
                                <p style="font-size:16px; font-weight:bold;">
                                    <?php echo acentos($_POST['nombre']);?>
                                </p>
                                <p class="amarillo" style="font-size:14px; font-weight:bold;">
                                    <?php echo acentos($_POST['cargo']);?>
                                </p>
                                <p style="text-decoration:underline; font-size:15px;">
                                    <?php echo acentos($_POST['email']);?>
                                </p>
                            </td>
                            <td style="width:342px;">
                                <p style="font-size:14px; text-align:right; margin-top: 9px; <?php if (!isset($_POST['opcional'])&& empty($_POST['opcional'])){ echo "margin-bottom: 10px;";}?>">
                                    T. <?php echo $telefono[$_POST['oficina']];?>  Ext. <?php echo $_POST['anexo'];?>
                                </p>

                                <p style="font-size:13px; text-align:right; <?php if (!isset($_POST['opcional'])&& empty($_POST['opcional'])){ echo "margin-bottom: 9px;";}?>">
                                    <?php echo acentos($local[$_POST['oficina']]);?>
                                </p>
                                <?php
                                    if (isset($_POST['opcional'])&&!empty($_POST['opcional'])){
                                    ?>

                                    <p style="font-size:13px; text-align:right;">
                                        <?php echo acentos($_POST['opcional']);?>
                                    </p>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="height:10px; line-height: 10px;" colspan="2">
                    <img src="http://vipac.pe/firmas/2014/blank.gif" style="width:100%; height: 1px;"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <a href="http://vipac.pe/firmas/2014/frase.php?idioma=<?php echo $_POST['idioma'];?>" target="_blank"><img src="http://vipac.pe/firmas/2014/<?php echo $frase[$_POST['idioma']];?>" style="width:613px; height: 87px;"/></a>
                </td>
            </tr>
            <tr>
                <td style="height:5px; line-height: 5px;" colspan="2">
                    <img src="http://vipac.pe/firmas/2014/blank.gif" style="width:496px; height: 1px;"/>
                </td>
            </tr>
            <tr>
                <td align="center" style="width:151px; margin:0px; background: #000;">
                    <table style="width:133px; height: 39px;">
                        <tr>
                            <td>
                                <img src="http://vipac.pe/firmas/2014/<?php echo $siguenos[$_POST['idioma']];?>" style="width:58px; height: 40px;" />
                            </td>
                            <td>
                                <a href="https://www.facebook.com/vipacperu">
                                    <img src="http://vipac.pe/firmas/2014/face.png" style="width:18px; height: 19px;" />
                                </a>
                            </td>
                            <td>
                                <a href="http://blog.vipac.pe/" target="_blank">
                                    <img src="http://vipac.pe/firmas/2014/blog.png" style="width:18px; height: 19px;" />
                                </a>
                            </td>
                        </tr>

                    </table>
                </td>
                <td style="width:465px; margin:0px;">
                    <a href="http://vipac.pe/firmas/2014/publi.php?idioma=<?php echo $_POST['idioma'];?>" target="_blank"><img src="http://vipac.pe/firmas/2014/publi/<?php echo $publi[$_POST['idioma']];?>" style="width:465px; height:70px;" /></a>
                </td>
            </tr>
            <tr>
                <td style="height:5px; line-height: 5px;" colspan="2">
                    <img src="http://vipac.pe/firmas/2014/blank.gif" style="width:100%; height:1px;" />
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center" bgcolor="#FFFFFF">
                    <p class="verde" style="font-size:13px;"><img src="http://vipac.pe/firmas/2014/hoja.png" style="width:16px; height:15px;" /> <?php echo acentos($ambiental[$_POST['idioma']]);?></p>
                </td>
            </tr>
        </table>
        </body>

        </html>
    </textarea>
</body>

</html>

<?php
    unset($_POST['nombre']);
    unset($_POST['email']);
    unset($_POST['anexo']);
    unset($_POST['cargo']);
    unset($_POST['opcional']);
}

