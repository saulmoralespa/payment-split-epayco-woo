<?php

return apply_filters('payment_split_epayco_settings',
    array(
        'enabled' => array(
            'title' => __('Habilitar/Deshabilitar'),
            'type' => 'checkbox',
            'label' => __('Habilitar Split ePayco'),
            'default' => 'no'
        ),
        'title' => array(
            'title' => __('Title'),
            'type' => 'text',
            'description' => __('Corresponde al título que el usuario ve durante el checkout'),
            'default' => __('Split ePayco'),
            'desc_tip' => true,
        ),
        'description' => array(
            'title' => __('Description'),
            'type' => 'textarea',
            'description' => __('Corresponde a la descripción que el usuario verá durante el checkout'),
            'default' => __('Split ePayco'),
            'desc_tip' => true,
        ),
        'debug' => array(
            'title' => __('Debug'),
            'type' => 'checkbox',
            'label' => __('Registros de depuración, se guarda en el registro de pago'),
            'default' => 'no'
        ),
        'environment' => array(
            'title' => __('Environment'),
            'type'        => 'select',
            'class'       => 'wc-enhanced-select',
            'description' => __('Habilitar para ejecutar pruebas'),
            'desc_tip' => true,
            'default' => true,
            'options'     => array(
                false    => __( 'Production' ),
                true => __( 'Test' ),
            ),
        ),
        'custIdCliente' => array(
            'title' => __('P_CUST_ID_CLIENTE'),
            'type' => 'text',
            'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
            'default' => '',
            'desc_tip' => true,
            'placeholder' => ''
        ),
        'pKey' => array(
            'title' => __('P_KEY'),
            'type' => 'text',
            'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
            'default' => '',
            'desc_tip' => true,
            'placeholder' => ''
        ),
        'apiKey' => array(
            'title' => __('PUBLIC_KEY'),
            'type' => 'text',
            'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
            'default' => '',
            'desc_tip' => true,
            'placeholder' => ''
        ),
        'privateKey' => array(
            'title' => __('PRIVATE_KEY'),
            'type' => 'password',
            'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
            'default' => '',
            'desc_tip' => true,
            'placeholder' => ''
        ),
        'comisionTitle'          => array(
            'title'       => __( 'Valor de la comisión porcentual' ),
            'type'        => 'title',
            'description' => __( 'Valor de la comisión que recibirá como comercio principal' )
        ),
        'comision' => array(
            'title' => __('Porcentaje comisión'),
            'type' => 'number',
            'description' => __('Porcentaje comisión a recibir'),
            'default' => 0,
            'desc_tip' => true,
            'placeholder' => '',
            'custom_attributes' => array(
                'step' => 1,
                'min'  => 0,
                'max' => 100
            )
        )
    )
);