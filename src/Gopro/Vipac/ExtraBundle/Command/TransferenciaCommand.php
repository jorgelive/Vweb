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
        $this->setName('gopro:extra:transferenciacb')
            ->setDescription('Enviar correos sobre operación');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $datos=$this->getContainer()->get('gopro_dbproceso_proceso');
        $datos->setConexion($this->getContainer()->get('doctrine.dbal.vipac_connection'));
        $datos->setTabla('EXTRA_TRANSFERENCIA_CB');
        $datos->setSchema('VWEB');
        $datos->setCamposSelect([
            'ID',
            'CUENTA_ORIGEN',
            'TIPO_ORIGEN',
            'NUMERO_ORIGEN',
            'MONTO_ORIGEN',
            'FCH_HORA_CREACION',
            'IND_PROCESO'
        ]);
        $datos->setCamposCustom([
            'CUENTA_ORIGEN',
            'TIPO_ORIGEN',
            'NUMERO_ORIGEN'
        ]);
        $datos->setQueryVariables(['IND_PROCESO'=>'n']);

        if(!$datos->ejecutarSelectQuery()||empty($datos->getExistentesRaw())){
            $output->writeln('No hay datos que procesar');
            return false;
        }
        $datosTransferencia=$this->getContainer()->get('gopro_dbproceso_proceso');
        $datosTransferencia->setConexion($this->getContainer()->get('doctrine.dbal.vipac_connection'));
        $datosTransferencia->setTabla('VVW_TRANSFERENCIA_CB');
        $datosTransferencia->setSchema('VIAPAC');
        $datosTransferencia->setCamposSelect([
            'CONSECUTIVO',
            'CUENTA_ORIGEN',
            'TIPO_ORIGEN',
            'NUMERO_ORIGEN',
            'CUENTA_DESTINO',
            'MONTO_DESTINO',
            'MONEDA',
            'BENEFICIARIO',
            'CONCEPTO',
            'TIPO_MOV',
            'CONTRIBUYENTE',
            'PROVEEDOR',
            'NOMBRE',
            'ALIAS',
            'E_MAIL',
            'CATEGORIA_PROVEED',
            'PAIS'
        ]);
        $datosTransferencia->setQueryVariables($datos->getExistentesCustom());

        if(!$datosTransferencia->ejecutarSelectQuery()||empty($datosTransferencia->getExistentesRaw())){
            $output->writeln('No hay datos coincidentes con la tabla de proceso');
            return false;
        }

        $resultado = $this->getContainer()->get('gopro_main_variable')->utf($datosTransferencia->getExistentesRaw());

        foreach($resultado as $linea):
            if(!empty(trim($linea['E_MAIL']))){
                $mails[$linea['CONTRIBUYENTE']]['email'][]=trim($linea['E_MAIL']);
            }
            if(!isset($mails[$linea['CONTRIBUYENTE']]['nombre'])){
                $mails[$linea['CONTRIBUYENTE']]['nombre']=trim($linea['NOMBRE']);
            }
            if(!isset($mails[$linea['CONTRIBUYENTE']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['tipo'])){
                $mails[$linea['CONTRIBUYENTE']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['tipo']=$linea['TIPO_ORIGEN'];
            }
            if(!isset($mails[$linea['CONTRIBUYENTE']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['numero'])){
                $mails[$linea['CONTRIBUYENTE']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['numero']=$linea['NUMERO_ORIGEN'];
            }
            if(!isset($mails[$linea['CONTRIBUYENTE']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['items'][$linea['CONSECUTIVO']])){
                $mails[$linea['CONTRIBUYENTE']]['operaciones'][$linea['TIPO_ORIGEN'].'|'.$linea['NUMERO_ORIGEN']]['items'][$linea['CONSECUTIVO']]=$linea;
            }
        endforeach;

        foreach ($mails as $mail):
            $mailAdressCco=$this->getContainer()->getParameter('mailer.cco');
            if(isset($mail['email'])&&count($mail['email'])!=0){
                $mailAddress=$mail['email'];
            }else{
                $mailAddress=array($mailAdressCco);
            }

            //$mailAdressCco='jgomez@vipac.pe';
            //$mailAddress=array('jgomez@vipac.pe');
            $mensage = \Swift_Message::newInstance()
                ->setSubject('Programacion de Pago')
                ->setFrom(array($this->getContainer()->getParameter('mailer_user') => 'Contabilidad Viajes Pacífico'))
                ->setTo(array_unique($mailAddress))
                ->setBcc($mailAdressCco)
                ->setContentType("text/html")
                ->setBody(
                    $this->getContainer()->get('templating')->render(
                        'GoproVipacExtraBundle:Transferencia:transferencia.html.twig',
                        array('contenido' => $mail)
                    )
                )
            ;
            $this->getContainer()->get('mailer')->send($mensage);
            $output->writeln('Se ha realizado el envio a: '.$mail['nombre'].' con e-mail: '.implode(', ',array_unique($mailAddress)));
        endforeach;

        $datos->setWhereUpdateValores($datosTransferencia->getWhereSelectValores());
        $datos->setWhereUpdatePh($datosTransferencia->getWhereSelectPh());
        $datos->setQueryVariables(['IND_PROCESO'=>'s'],'valoresUpdate');
        $datos->ejecutarUpdateQuery();
    }
}

