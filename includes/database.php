<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\helpers\State;

class Database{
    private $wpdb;
    private $table_enqueu;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_enqueu   = $this->wpdb->prefix.'dcms_enqueu_email';
    }

    // Init activation create table
    public function create_table_enqueu(){
        $sql = " CREATE TABLE IF NOT EXISTS {$this->table_enqueu} (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `data` mediumtext DEFAULT NULL,
                    `status` tinyint DEFAULT 0,
                    `type` varchar(50) DEFAULT NULL,
                    `resend` boolean DEFAULT FALSE,
                    `created` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
            )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Get sent email, status = 1
    public function get_sent_emails(){
        $sent = State::sent;
        return $this->get_emails_by_status($sent, DCMS_ENQUEU_SHOW_MAX_LOG_ROWS, 'updated', 'DESC');
    }

    // Get error email, status = -1
    public function get_error_emails(){
        $error = State::error;
        return $this->get_emails_by_status($error, DCMS_ENQUEU_SHOW_MAX_LOG_ROWS, 'updated', 'DESC');
    }

    // Get pendieng email, status = 0
    public function get_pending_emails( $quantity = false){
        $pending = State::pending;
        return $this->get_emails_by_status($pending, $quantity, 'created', 'ASC' );
    }

    // Get email by status, 0 = pending, 1 = sent, -1 = error
    private function get_emails_by_status($status, $quantity, $order_field, $order_direction){
        $limit = $quantity
                    ? ' limit 0, '. $quantity
                    : '';

        $sql = $this->wpdb->prepare("SELECT * FROM $this->table_enqueu
                                WHERE status = $status
                                ORDER BY $order_field $order_direction $limit");

        $result = $this->wpdb->get_results($sql);

        return $result;
    }

    // Save data
    public function insert_email_data( $type, $data ){
        $item = [];
        $item['type'] = $type;
        $item['data'] = $data;
        $item['created'] = date_i18n('Y-m-d H:i:s');

        return $this->wpdb->insert( $this->table_enqueu, $item, ['%s', '%s']);
    }

    // Udpate state, status =0 no sent, status = 1 send, status = -1 error
    public function update_email_status( $id, $status = 0 ){
        $data = [ 'status' => $status,
                  'updated' => date_i18n('Y-m-d H:i:s') ];
        $where = [ 'id' => $id ];

        return $this->wpdb->update($this->table_enqueu, $data, $where);
    }

}
