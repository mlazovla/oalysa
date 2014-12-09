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
    
    /**
     * Returns grades of this subject.
     * @param int $subjectId
     * @return Grade[] $grades
     */
    public function getGrades($subjectId) {
        $s2g = new Subject2Grade($this->db);
        $temp = $s2g->where('Subject_id = '. $subjectId);
        if ($temp == null) return null;
        
        /**
         * @var Grade[] $grades
         */
        $grades = null;
        foreach ($temp as $t) {
            $temp2 = new Grade($this->db);
            $grades[] = $t->grade;
            //$grades[] = $temp2->get($t->grade_id);
        }
        return $grades;       
    }
}
