<?php

/**
 * Form to checker messages
 * @author royopa - <royopa@gmail.com>
 */

namespace Royopa\DSpace\i18n\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class FormChecker extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('master', 'choice', array(
                'label' => 'Master *',
                'label_attr' => array('class' => 'col-sm-3 control-label'),
                'attr' => array('class' => 'form-control'),
                'choices' => $this->getLanguages(),
                'required' => true,
                'expanded' => false,
                'data' => 'messages.xml',
            ))
            ->add('toCheck', 'choice', array(
                'label' => 'To Check *',
                'label_attr' => array('class' => 'col-sm-3 control-label'),
                'attr' => array('class' => 'form-control'),
                'choices' => $this->getLanguages(),
                'required' => true,
                'expanded' => false,
                'empty_data'  => null,
                'placeholder' => 'Choose an option',
            ))
            ->add('Check', 'submit', array(
                'attr' => array('class' => 'btn btn-success')
            ))
        ;
    }

    public function getName()
    {
        return 'form_checker';
    }

    private function getLanguages()
    {
        // some default data for when the form is displayed the first time
        $languagesAvailable = array(
            'messages.xml' => 'en (master)',
            'messages_ar.xml' => 'ar',
            'messages_bg.xml' => 'bg',
            'messages_ca.xml' => 'ca',
            'messages_ca_ES.xml' => 'ca_ES',
            'messages_cs.xml' => 'cs',
            'messages_de.xml' => 'de',
            'messages_el.xml' => 'el',
            'messages_es.xml' => 'es',
            'messages_et.xml' => 'et',
            'messages_eu.xml' => 'eu',
            'messages_fr.xml' => 'fr',
            'messages_gl.xml' => 'gl',
            'messages_id.xml' => 'id',
            'messages_it.xml' => 'it',
            'messages_ja.xml' => 'ja',
            'messages_pl.xml' => 'pl',
            'messages_pt_BR.xml' => 'pt_BR',
            'messages_ru.xml' => 'ru',
            'messages_tr.xml' => 'tr',
            'messages_uk.xml' => 'uk',
        );

        return $languagesAvailable;
    }
}
