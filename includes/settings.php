<?php

namespace dcms\enqueu\includes;

/**
 * Class for creating the settings
 */
class Settings{

    public function __construct(){
        add_action('admin_init', [$this, 'init_configuration']);
    }

    // Register seccions and fields
    public function init_configuration(){
        register_setting('dcms_enqueu_options_bd', DCMS_ENQUEU_OPTIONS , [$this, 'validate_number']);

        $this->fields_enqueue();
    }

    // New user fields
    private function fields_enqueue(){

        add_settings_section('dcms_enqueue_section',
                        __('Configuración cola de correos', 'dcms-enqueu-email'),
                                [$this,'dcms_section_cb'],
                                'dcms_enqueue_sfields' );

        add_settings_field('dcms_cron_interval',
                            __('Intervalo CRON', 'dcms-enqueu-email'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_enqueue_sfields',
                            'dcms_enqueue_section',
                            [
                                'dcms_option' => DCMS_ENQUEU_OPTIONS,
                                'label_for' => 'dcms_cron_interval',
                                'required' => true,
                                'description' => 'Ingresar un número en minutos, mínimo 10'
                            ]
        );

        add_settings_field('dcms_quantity_batch',
                            __('Cantidad por lote', 'dcms-enqueu-email'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_enqueue_sfields',
                            'dcms_enqueue_section',
                            [
                              'dcms_option' => DCMS_ENQUEU_OPTIONS,
                              'label_for' => 'dcms_quantity_batch',
                              'required' => true,
                              'description' => 'Cantidad de correos que se procesarán por cada intervalo cron'
                            ]
        );

    }

    // Métodos auxiliares genéricos

    // Callback section
    public function dcms_section_cb(){
		echo '<hr/>';
	}

    // Callback input field callback
    public function dcms_section_input_cb($args){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $req = isset($args['required']) ? 'required' : '';
        $class = isset($args['class']) ? "class='".$args['class']."'" : '';
        $desc = isset($args['description']) ? $args['description'] : '';

        $options = get_option( $dcms_option );
        $val = isset( $options[$id] ) ? $options[$id] : '';

        printf("<input id='%s' name='%s[%s]' class='regular-text' type='text' value='%s' %s %s>",
                $id, $dcms_option, $id, $val, $req, $class);

        if ( $desc ) printf("<p class='description'>%s</p> ", $desc);

    }


    public function validate_number( $input ){
        $output = [];

        $output['dcms_cron_interval'] = abs(intval($input['dcms_cron_interval']));
        $output['dcms_quantity_batch'] = abs(intval($input['dcms_quantity_batch']));

        return $output;
    }

}
