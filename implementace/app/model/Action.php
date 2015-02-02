<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých komentářích.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar() name
 *  - varchar() type
 *  
 */
class Action extends \Nette\Database\Table\Selection {
    private $table = "action";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
