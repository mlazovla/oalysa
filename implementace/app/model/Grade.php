<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých ročnících.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar() name
 *  - int gradeNext FK
 *  
 */
class Grade extends \Nette\Database\Table\Selection {
    private $table = "Grade";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
