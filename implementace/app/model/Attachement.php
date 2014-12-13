<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých přiložených souborechke článkům.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar(127) name
 *  - varchar(255) file
 *  - int topic_id FK
 *  - int user_id FK
 *  - text description
 *  
 */
class Attachement extends \Nette\Database\Table\Selection {
    private $table = "Attachement";
    private $db;
    /**
     * The Path in $basePath
     * @var string
     */
    const SAVE_DIR = "attachements/";
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    public function getByTopic($topicId) {
        return $this->where('topic_id', $topicId);
    }
}
