<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o propojení Předmětů a Ročníků.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - int Subject_id FK
 *  - int Grade_id FK
 */
class Subject2Grade extends \Nette\Database\Table\Selection {
    private $table = "Subject2Grade";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
