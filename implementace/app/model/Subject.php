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
    private $table = "subject";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    /**
     * Returns grades of this subject.
     * @param int $subjectId
     * @return Grade[] $grades
     */
    public function getGrades($subjectId) {
        $s2g = new Subject2Grade($this->db);
        return $s2g->select('grade.name, grade.id')->where('subject_id', $subjectId)->order('grade.name');      
    }
}
