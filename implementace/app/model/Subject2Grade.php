<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o propojení Předmětů a Ročníků.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - int Subject_id FK
 *  - int Grade_id FK
 */
class Subject2Grade extends \Nette\Database\Table\Selection {
    private $table = "subject2grade";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    public function getRelationId($subject_id, $grade_id) {
        $s2g = new Subject2Grade($this->db);
        $res = $s2g->where('subject_id', $subject_id)->where('grade_id', $grade_id)->fetch();
        return (bool)$res['id'] ? $res['id'] : null;
    }
    
    /**
     * Update relation between one subject and Grades
     * @param int $subject_id id of Subject to change
     * @param array $grade_ids ids of related Grades
     */
    public function updateRelations($subject_id, $grade_ids = null) {
        // set empty array to related grades
        if ($grade_ids == null) {
            $grade_ids = array();
        }

        // init
        $grade = new Grade($this->db);
        $grade = $grade->select('id');
        $subject2grade = new Subject2Grade($this->db);       
        
        $selectedGrades = array();
        foreach($grade_ids as $sg) {
            $selectedGrades[$sg] = true;
        }     
        
        // for every potencial related between one Subject and any Grade
        foreach ($grade as $g) {
            $existingRecord_id = $subject2grade->getRelationId($subject_id, $g); //try to read existing record
            // if the record would exist
            if(isset($selectedGrades[(string)$g])) {
                if (!$existingRecord_id) { // if record do not exist instert it
                    $subject2grade = new Subject2Grade($this->db);
                    $subject2grade->insert(array(
                        'subject_id' => $subject_id,
                        'grade_id' => $g
                    ));
                }
            }
            // if the record would not exist
            else {
                if ($existingRecord_id) { // if record exist delete it
                    $subject2grade = new Subject2Grade($this->db);
                        $subject2grade->where('id', $existingRecord_id)->delete();
                }                            
            }     
        }
    }
}
