<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých přiložených souborechke článkům.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar(127) name
 *  - varchar(255) file
 *  - varchar(255) mimeType
 *  - int topic_id FK
 *  - int user_id FK
 *  - DateTime created_at
 *  - text description
 *  
 */
class Attachement extends \Nette\Database\Table\Selection {
    private $table = "Attachement";
    private $db;
    /**
     * Path to attachements
     * @var string
     */
    const SAVE_DIR = "../data/attachements/";
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    /**
     * Get all attachements to Topic
     * @param int $topicId
     * @return Attachement[]
     */
    public function getByTopic($topicId) {
        return $this->where('topic_id', $topicId);
    }

    /**
     * Get path to Attachemement
     * @param int $attachementId
     * @return string
     */
    public function getPathById($attachementId) {
        $path = $this->get($attachementId)->file;
        if (!$path) {
            return null;
        }
        return self::SAVE_DIR . $path;
    }
    
    /**
     * Get file extension example: pdf
     * @param int $attachementId
     * @return string
     */
    public function getExtensionById($attachementId) {
        $filename = (string) $this->get($attachementId)->file;
        $tmp = explode($filename, '.');
        return end($tmp);
    }
}
