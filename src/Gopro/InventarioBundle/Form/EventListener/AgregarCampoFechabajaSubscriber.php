<?php
namespace Gopro\InventarioBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AgregarCampoFechabajaSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // check if the product object is "new"
        // If you didn't pass any data to the form, the data is "null".
        // This should be considered a new "Product"
        if (!$data || !$data->getId()) {
            $form->add('fechabaja',null, array(
                'label' => 'Baja',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'disabled'=> true
            ));
        }else{
           $form->add('fechabaja',null, array(
                'label' => 'Baja',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker')
            ));
        }
    }
}