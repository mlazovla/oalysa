<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých novinkách.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - DateTime created_at
 *  - int user_id FK
 *  - text content
 */
class News extends \Nette\Database\Table\Selection {
    private $table = "News";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}

