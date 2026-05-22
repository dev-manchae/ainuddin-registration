<?php

require_once "config/database.php";

class BaseModel {
    
    protected $db;

    public function __construct() {
        $this->db = getConnection();
    }

    /**
     * Run a select query and fetch all rows
     */
    protected function fetchAll($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Run a select query and fetch a single row
     */
    protected function fetch($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Run an insert/update/delete query
     */
    protected function execute($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get last inserted ID
     */
    protected function lastId() {
        return $this->db->lastInsertId();
    }
}