<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\includes\Process;


class Cron{
    private $process;
    private $interval_cron;
    private $enable_enqueu;

    public function __construct(){

        $options = get_option(DCMS_ENQUEU_OPTIONS);
        $this->interval_cron = intval($options['dcms_cron_interval']);
        $this->enable_enqueu = $options['dcms_enable_queue'];

        $this->process = new Process();

        add_filter( 'cron_schedules', [ $this, 'dcms_custom_schedule' ]);
        add_action( 'dcms_enqueu_hook', [ $this, 'dcms_cron_process' ] );
    }

    // Add new schedule
    public function dcms_custom_schedule( $schedules ) {
        $schedules['dcms_enqueu_interval'] = array(
            'interval' => $this->interval_cron*60,
            'display' => ($this->interval_cron*60) . ' seconds'
        );
        return $schedules;
    }

    // Cron process
    public function dcms_cron_process() {
        if ( $this->enable_enqueu ){
            $this->process->process_sent();
        }
    }
}