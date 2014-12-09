<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých předmětech.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar(15) shortcut
 *  - varchar(127) name
 *  - text description
 *  
 */
class Subject extends \Nette\Database\Table\Selection {
    private $table = "Subject";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
