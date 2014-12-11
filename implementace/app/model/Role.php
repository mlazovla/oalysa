<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých Rolích.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar name
 *  - int inherit FK
 */
class Role extends \Nette\Database\Table\Selection {
    private $table = "Role";
    private $db;

    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table,
            $database->getDatabaseReflection());
        $this->db = $database;
    }
}
