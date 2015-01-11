<?php

namespace App\Model;

/**
 * Třída obstarávající přístup k uživatelským účtům v databázi.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar() username
 *  - varchar() password
 *  - varchar() name
 *  - Role
 *  - Grade
 */
class Acount extends \Nette\Database\Table\Selection {
    private $table = "user";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    public function add($username, $password, $role_id, $name="", $email="") {
        $acount = new Acount($this->db);       
        if ($acount->where('username', $username)->count('id') != 0) {
            return false;
        }
        else {
            $user = new UserManager($this->db);
            $user->add($username, $password, $role_id, $name, $email);
            return true;
        }        
    }
}
