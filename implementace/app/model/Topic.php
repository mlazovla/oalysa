<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o Tématech.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar name
 *  - DateTime created_at
 *  - int subject2grade_id FK
 *  - int user_id FK
 *  - bool isPublic
 *  - text anotation
 *  - text content
 */
class Topic extends \Nette\Database\Table\Selection {
    private $table = "Topic";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    public function getSubject($idTopic) {
        return $this->get($idTopic)->subject2grade->subject;
    }
    
    public function getGrade($idTopic) {
        return $this->get($idTopic)->subject2grade->grade;
    }
    
    public function safeDelete($topic_id) {
        $attachement = new Attachement($this->db);
        $attachements = $attachement->select('id')->where('topic_id', $topic_id)->fetchAll();
        foreach($attachements as $a) {
            $attachement->safeDelete($a['id']);
        }
        $this->where('id', $topic_id)->delete();
    }
}
