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
    private $table = "role";
    private $db;

    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table,
            $database->getDatabaseReflection());
        $this->db = $database;
    }

    /**
     * Translate role name to role id.
     * @param string $role_name
     * @return int
     */
    public function roleName2id($role_name) {
        $res = $this->where('name', $role_name)->limit(1)->fetch();
        return $res['id'];
    }
    
    /**
     * Get list of all parent roles
     * 
     * @param mixed $role_id
     * @return array[int]{id, role_name}
     */
    public function getAllParents($role_id_or_id) {
        if (is_int($role_id_or_id))
            $role_id = $role_id_or_id;
        else
            $role_id = $this->roleName2id($role_id_or_id);
        
        $i = 0;
        while($role_id != null && $i < 10) {
            $i++;
            $role = new Role($this->db);
            $tmp = $role->where('id', $role_id)->limit(1)->fetch();
            $res[] = array('id' => $tmp['id'], 'name' => $tmp['name'], 'role_id' => $tmp['role_id']);
            $role_id = $tmp['role_id'];
            unset($role);
        }
        return $res;
    }
}
