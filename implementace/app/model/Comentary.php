<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých komentářích.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - int user_id FK
 *  - int topic_if FK
 *  - int answer_on FK
 *  - DateTime created_at
 *  - text content
 *  
 */
class Comentary extends \Nette\Database\Table\Selection {
    private $table = "Comentary";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
}
