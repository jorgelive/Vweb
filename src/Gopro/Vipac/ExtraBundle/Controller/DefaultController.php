<?php

namespace Gopro\Vipac\ExtraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/firma", name="gopro_vipac_extra_firma")
     * @Template()
     */
    public function firmaAction(Request $request)
    {
        $datos = array();
        $textarea='';
        $body='';
        $css='';
        $oficinaChOp = array('reducto'=>'Reducto','lamar'=>'La Mar','cusco'=>'Cusco','app'=>'Arequipa');
        $oficinaCh=array('choices'=>$oficinaChOp,'multiple'=>false,'expanded'=>true);
        $idiomaChOp = array('es'=>'Español','en'=>'Inglés','pt'=>'Portugués');
        $idiomaCh=array('choices'=>$idiomaChOp,'multiple'=>false,'expanded'=>true);

        $formulario = $this->createFormBuilder($datos)
            ->add('Nombre', 'text')
            ->add('E-mail', 'text')
            ->add('Cargo', 'text')
            ->add('Anexo', 'text')
            ->add('Opcional', 'text')
            ->add('Oficina', 'choice', $oficinaCh)
            ->add('Idioma', 'choice', $idiomaCh)
            ->getForm();

        if ($request->isMethod('POST')) {
            $formulario->handleRequest($request);
            $data = $formulario->getData();

$markupOpen='<html lang="es">';
$markupOpen.='<head>';
    $markupOpen.='<meta charset="utf-8"/>';
    $markupOpen.='<meta name=GENERATOR content="MSHTML 8.00.6001.19170">';
    $markupOpen.='<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">';
    $markupOpen.='<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">';
    $css='<style type="text/css">';
        $css.='#firma *{margin: 0; padding: 0; border: 0; }';
        $css.='#firma { text-align: left; font-family: calibri; font-size: 11px; line-height: 11px;}';
        $css.='#firma table, #firma tr, #firma td {padding: 0; border: 0; border-collapse: collapse; }';
        $css.='#firma a { margin: 0; padding: 0; font-family: calibri; text-decoration: none; border: 0;}';
        $css.='#firma a:hover { text-decoration: underline; }';
        $css.='#firma p { color:#474747; margin: 0; padding: 0; font-size: 14px; text-align: justify; text-decoration: none }';
        $css.='#firma .plomo{ color:#474747;}';
        $css.='#firma .verde{ color:#060;}';
        $css.='textarea.codigo{ border:#000000 solid 1px; margin-left:10px; width:600px; height: 1000px;}';
    $css.='</style>';
$markupMiddle='</head>';
$markupMiddle.='<body>';
$body='<div id="firma" class="plomo">';
$body.='<table style="width: 613px;">';
    $body.='<tr>';
    $body.='<td>&nbsp;</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td colspan="2">';
            $body.='<table style="width:100%;">';
                    $body.='<td style="width:240px;">';
                        $body.='<p style="font-size:16px; font-weight:bold;">';
                            $body.=$data['Nombre'];
                        $body.='</p>';
                        $body.='<p class="amarillo" style="font-size:14px; font-weight:bold;">';
                            $body.=$data['Cargo'];
                        $body.='</p>';
                        $body.='<p style="text-decoration:underline; font-size:15px;">';
                            $body.=$data['E-mail'];
                        $body.='</p>';
                    $body.='</td>';
                    $body.='<td style="width:342px;">';
                        if (empty($data['Opcional'])){ $temp1='margin-bottom: 10px;';}else{$temp1='';}
                        $body.='<p style="font-size:14px; text-align:right; margin-top: 9px;'.$temp1.'">';
                            $body.='T.'.$this->getTraduccion('telefono',$data['Oficina']);
                            if (!empty($data['Anexo'])){$body.= " Ext. ".$data['Anexo'];}
                        $body.='</p>';
                        $body.='<p style="font-size:13px; text-align:right;'.$temp1.'">';
                            $body.=$this->getTraduccion('local',$data['Oficina']);
                        $body.='</p>';
                        if (!empty($_POST['opcional'])){
                            $body.='<p style="font-size:13px; text-align:right;">';
                                $body.=$data['Opcional'];
                            $body.='</p>';
                        }

                    $body.='</td>';
                $body.='</tr>';
            $body.='</table>';
        $body.='</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td style="height:10px; line-height: 10px;" colspan="2">';
            $body.='<img src="http://vipac.pe/firmas/2014/blank.gif" style="width:100%; height: 1px;"/>';
        $body.='</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td colspan="2">';
            $body.='<a href="http://vipac.pe/firmas/2014/frase.php?idioma='.$data['Idioma'].'" target="_blank"><img src="http://vipac.pe/firmas/2014/'.$this->getTraduccion('frase',$data['Idioma']).'" style="width:613px; height: 87px;"/></a>';
        $body.='</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td style="height:5px; line-height: 5px;" colspan="2">';
            $body.='<img src="http://vipac.pe/firmas/2014/blank.gif" style="width:496px; height: 1px;"/>';
        $body.='</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td align="center" style="width:151px; margin:0px; background: #000">';
            $body.='<table style="width:133px; height: 39px;">';
                $body.='<tr>';
                    $body.='<td>';
                        $body.='<img src="http://vipac.pe/firmas/2014/'.$this->getTraduccion('siguenos',$data['Idioma']).'" style="width:58px; height: 40px;" />';
                    $body.='</td>';
                    $body.='<td>';
                        $body.='<a href="https://www.facebook.com/vipacperu">';
                            $body.='<img src="http://vipac.pe/firmas/2014/face.png" style="width:18px; height: 19px;" />';
                        $body.='</a>';
                    $body.='</td>';
                    $body.='<td>';
                        $body.='<a href="http://blog.vipac.pe/" target="_blank">';
                            $body.='<img src="http://vipac.pe/firmas/2014/blog.png" style="width:18px; height: 19px;" />';
                        $body.='</a>';
                    $body.='</td>';
                $body.='</tr>';
            $body.='</table>';
        $body.='</td>';
        $body.='<td style="width:465px; margin:0px;">';
            $body.='<a href="http://vipac.pe/firmas/2014/publi.php?idioma='.$data['Idioma'].'" target="_blank"><img src="http://vipac.pe/firmas/2014/publi/'.$this->getTraduccion('publi',$data['Idioma']).'" style="width:465px; height:70px;" /></a>';
        $body.='</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td style="height:5px; line-height: 5px;" colspan="2">';
            $body.='<img src="http://vipac.pe/firmas/2014/blank.gif" style="width:100%; height:1px;" />';
        $body.='</td>';
    $body.='</tr>';
    $body.='<tr>';
        $body.='<td colspan="2" align="center" bgcolor="#FFFFFF">';
            $body.='<p class="verde" style="font-size:13px;"><img src="http://vipac.pe/firmas/2014/hoja.png" style="width:16px; height:15px;" />'.$this->getTraduccion('ambiental',$data['Idioma']).'</p>';
        $body.='</td>';
    $body.='</tr>';
$body.='</table>';
$body.='</div>';
$markupClose='</body>';
$markupClose.='</html>';
            $textarea=$markupOpen.$css.$markupMiddle.$body.$markupClose;
        }


        return array('formulario' => $formulario->createView(),'textarea' => $textarea,'css' => $css, 'body'=> $body);
    }

    private function getTraduccion($variable,$idioma){
        $var['local']['reducto']= "Av. Paseo de la República 6010 - Piso 7 - Lima 18, Perú";
        $var['local']['lamar']="Av. la Mar 163 - Lima 18, Perú";
        $var['local']['cusco']="Av. El Sol 817 - Cusco, Perú";
        $var['local']['aqp']="Calle Palacio Viejo 216 Of. 101 Cercado - Arequipa, Perú";
        $var['telefono']['reducto']="+51 1 610 1900";
        $var['telefono']['lamar']="+51 1 610 1900";
        $var['telefono']['cusco']="+51 84 221744";
        $var['telefono']['aqp']="+51 54 612267";
        $var['ambiental']['es']="Piensa en el planeta antes de imprimir. Reduce. Reusa. Recicla";
        $var['ambiental']['en']="Think before printing. Reduce. Reuse. Recycle.";
        $var['ambiental']['pt']="Pense no planeta antes de imprimir. Reduzir. Reutilizar. Reciclar.";
        $var['frase']['es']="frase_esp.jpg";
        $var['frase']['en']="frase_eng.jpg";
        $var['frase']['pt']="frase_por.jpg";
        $var['publi']['es']="publi_esp.png";
        $var['publi']['en']="publi_eng.png";
        $var['publi']['pt']="publi_por.png";
        $var['siguenos']['es']="siguenos.png";
        $var['siguenos']['en']="follow.png";
        $var['siguenos']['pt']="siguenos_por.png";

        return $var[$variable][$idioma];

    }

    function getEntityOfText($text) {
        $reemplazo=array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&uuml;","&ntilde;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Uuml;","&Ntilde;");
        $original = array("á","é","í","ó","ú","ü","ñ","Á","É","Í","Ó","Ú","Ü","Ñ");
        $text = htmlentities(str_replace($original, $reemplazo, $text));
        return $text;
    }
}
