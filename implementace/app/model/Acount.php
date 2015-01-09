<?php

namespace App\Model;

/**
 * Třída obstarávající přístup k uživatelským účtům v databázi.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar() username
 *  - varchar() password
 *  - varchar() name
 *  - Role
 *  - Grade
 */
class Acount extends \Nette\Database\Table\Selection {
    private $table = "user";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
