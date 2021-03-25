<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditAliasForm extends Form
{

    public function init()
    {
        $this->setName('epf');

        $this->add(array(
            'name' => 'epf-alias',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-alias',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Alias',
                'notes' => 'Arbitrary name or identifier for this user',
            ),
        ));

        $this->add(array(
            'name' => 'epf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Alias ändern',
                'class' => 'default-button',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'epf-alias' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type a name here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'The name should be at least %min% characters long',
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                return !is_numeric($value);
                            },
                            'message' => 'The name must not be a number',
                        ),
                    ),
                ),
            )
        )));
    }

}