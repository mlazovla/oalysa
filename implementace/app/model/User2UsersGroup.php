<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o propojení Uživatelů a Skupin.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - int User_id FK
 *  - int UsersGroup_id FK
 */
class User2UsersGroup extends \Nette\Database\Table\Selection {
    private $table = "User2UsersGroup";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
