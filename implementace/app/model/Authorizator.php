<?php
namespace App\Model;

use \App\Model\Role;

class MyAuthorizator extends \Nette\Object
implements \Nette\Security\IAuthorizator
{
    private $database;
    private $roles;
    
    const GARANTED_PRIVILEGIES_TABLE = 'garantedprivilegies';
    const ROLE_COLUMB = 'role';
    const ROLE_TABLE = 'role';
    const RESOURCE_COLUMB = 'resource';
    const RESOURCE_TABLE = 'resource';
    const PRIVILEGE_COLUMB = 'privilege';
    const PRIVILEGE_TABLE = 'privilege';
    
    public function injectDatabase(\Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \Nette\Security\IAuthorizator::isAllowed()
     */
    function isAllowed($role, $resource, $privilege = null)
    {
        // admin can everything
        if (strtolower($role) == 'admin' || strtolower($role) == 'administrator' || strtolower($role) == 'administrátor')
            return true;

        $roleModel = new Role($this->database);
        if (!$this->roles) {
            $this->roles = $roleModel->getAllParents($role);
        }
        $tmp = $this->database->table(self::RESOURCE_TABLE)->select('id')->where('name', $resource)->limit(1)->fetch();
        $resource_id = $tmp['id'];
        $tmp = $this->database->table(self::PRIVILEGE_TABLE)->select('id')->where('name', $privilege)->limit(1)->fetch();
        $privilege_id = $tmp['id'];
        
        if (!$resource_id) {
            return false;
        }

        
        $role_ids[] = $roleModel->roleName2id($role);
        foreach ($this->roles as $r) {
            $role_ids[] = $r['id'];
        }
        
        
        $where = "";
        foreach ($role_ids as $role_id) {
            $where .= 'role_id = ? OR ';
        }
        $where = substr($where, 0, strlen($where)-4);
        
        $finalResult = $this->database
            ->table(self::GARANTED_PRIVILEGIES_TABLE)
            ->where($where, $role_ids)
            ->where('resource_id', $resource_id);
        
        if ($privilege_id) {
            $finalResult->where('privilege_id', $privilege_id);
        }
        
        if ($finalResult->count('id') > 0) {
            return true;
        }
        else { 
            return false;
        }
    }

}
