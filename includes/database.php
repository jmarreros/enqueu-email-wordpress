<?php

namespace dcms\enqueu\includes;

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
                    `date` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
            )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Get pendieng email
    public function get_pending_emails( $qty = 0){

        $limit = $qty != 0
                    ? ' limit 0, '. $qty
                    : '';

        $sql = $this->wpdb->prepare("SELECT * FROM $this->table_enqueu
                                WHERE status = 0
                                ORDER BY id ASC $limit");

        $result = $this->wpdb->get_results($sql);
		return $result;
    }

    // Save data
    public function insert_email_data( $type, $data ){
        $item = [];
        $item['type'] = $type;
        $item['data'] = $data;

        return $this->wpdb->insert( $this->table_enqueu, $item, ['%s', '%s']);
    }

    // Udpate state, status =0 no sent, status = 1 send, status = -1 error
    public function update_email_status( $id, $status = 0 ){
        $data = [ 'status' => $status ];
        $where = [ 'id' => $id ];

        return $this->wpdb->update($this->table_enqueu, $data, $where);
    }

}
