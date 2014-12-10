<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých interakcích se systémem.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - int user_id FK
 *  - int topic_id FK
 *  - int action_id FK
 *  - DateTime created_at FK
 *  - text note
 */
class Log extends \Nette\Database\Table\Selection {
    private $table = "Log";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
