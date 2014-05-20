<?php
namespace Gopro\Vipac\ExtraBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransferenciaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('extra:enviartransferencia')
            ->setDescription('Enviar correos sobre operación');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $datos=$this->getContainer()->get('gopro_dbproceso_comun_cargador');
        $tablaD=array(
            'schema'=>'VWEB',
            'nombre'=>'EXTRA_TRANSFERENCIA_CB',
            'tipo'=>'S',
            'columnasProceso'=>Array('ID','CUENTA_ORIGEN','TIPO_ORIGEN','NUMERO_ORIGEN','MONTO_ORIGEN','FCH_HORA_CREACION','IND_PROCESO'),
            'llaves'=>Array('IND_PROCESO')
        );
        $columnaD['IND_PROCESO']=array('nombre'=>'IND_PROCESO','llave'=>'si');
        $valoresD[0]=array('IND_PROCESO'=>'n');
        $datos->setParametros($tablaD,$columnaD,$valoresD,$this->getContainer()->get('doctrine.dbal.vipac_connection'));
        $datos->ejecutar();
        if(empty($datos->getExistenteRaw())){
            $output->writeln('No hay datos que procesar');
            return false;
        }
        $datosTransferencia=$this->getContainer()->get('gopro_dbproceso_comun_cargador');
        $tablaDT=array(
            'schema'=>'VIAPAC',
            'nombre'=>'VVW_TRANSFERENCIA_CB',
            'tipo'=>'S',
            'columnasProceso'=>Array('CONSECUTIVO',
                'CUENTA_ORIGEN',
                'TIPO_ORIGEN',
                'NUMERO_ORIGEN',
                'CUENTA_DESTINO',
                'MONTO_DESTINO',
                'MONEDA',
                'BENEFICIARIO',
                'CONCEPTO',
                'TIPO_MOV',
                'PROVEEDOR',
                'NOMBRE',
                'ALIAS',
                'E_MAIL',
                'CATEGORIA_PROVEED',
                'PAIS'
            ),
            'llaves'=>Array('CUENTA_ORIGEN','TIPO_ORIGEN','NUMERO_ORIGEN')
        );
        $columnaDT['CUENTA_ORIGEN']=array('nombre'=>'CUENTA_ORIGEN','llave'=>'si');
        $columnaDT['TIPO_ORIGEN']=array('nombre'=>'TIPO_ORIGEN','llave'=>'si');
        $columnaDT['NUMERO_ORIGEN']=array('nombre'=>'NUMERO_ORIGEN','llave'=>'si');
        $datosTransferencia->setParametros($tablaDT,$columnaDT,$datos->getExistenteRaw(),$this->getContainer()->get('doctrine.dbal.vipac_connection'));
        $datosTransferencia->ejecutar();
        foreach($datosTransferencia->getExistenteRaw() as $linea):
            $mails[$linea['PROVEEDOR']]['email']=$linea['E_MAIL'];
            $mails[$linea['PROVEEDOR']]['nombre']=$linea['NOMBRE'];
            $mails[$linea['PROVEEDOR']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['tipo']=$linea['TIPO_ORIGEN'];
            $mails[$linea['PROVEEDOR']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['numero']=$linea['NUMERO_ORIGEN'];
            $mails[$linea['PROVEEDOR']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['items'][$linea['CONSECUTIVO']]=$linea;
        endforeach;

        foreach ($mails as $mail):
            $mailAdressCco=$this->getContainer()->getParameter('mailer.cco');
            if(!empty($mail['email'])){
                $mailAddress=$mail['email'];
            }else{
                $mailAddress=$mailAdressCco;
            }
            $mailAdressCco='jgomez@vipac.pe';
            $mailAddress='jgomez@vipac.pe';
            $mensage = \Swift_Message::newInstance()
                ->setSubject('Programacion de Pago')
                ->setFrom(array($this->getContainer()->getParameter('mailer_user') => 'Contabilidad Viajes Pacífico'))
                ->setTo($mailAddress)
                ->setBcc($mailAdressCco)
                ->setContentType("text/html")
                ->setBody(
                    $this->getContainer()->get('templating')->render(
                        'GoproVipacExtraBundle:transferencia:transferencia.html.twig',
                        array('contenido' => $mail)
                    )
                )
            ;
            $this->getContainer()->get('mailer')->send($mensage);
            $output->writeln('Se ha realizado el envio a: '.$mail['nombre'].' con e-mail: '.$mailAddress);
            //$output->writeln(print_r($mail));
        endforeach;

        //$output->writeln('Se ha realizado el envio');
    }
}

