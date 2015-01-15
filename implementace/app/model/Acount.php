<?php

namespace App\Model;

use Nette\Utils\Strings;
use Nette\Utils\DateTime;

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

    /**
     * Minimum lenght of password.
     * @var unknown
     */
    const MIN_PASSWORD_LEN = 5;
    
    private $table = "user";
    private $db;
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    public function add($username, $password, $role_id, $name="", $grade_id = null, $email="", $activate=1, $makeNameUnique = true) {
        $uniqueUsername = $username;
        $acount = new Acount($this->db);
        
        $i=0;
        while ($acount->where('username', $uniqueUsername)->count('id') != 0 && $i<10) {
            if (!$makeNameUnique) {
                return false;
            }
            $randMax = 9;
            $uniqueUsername = $username . rand(1,$randMax++);
            $acount = new Acount($this->db);
            $i++;
        }
       
        $user = new UserManager($this->db);
        $user->add($uniqueUsername, $password, $role_id, $name, $grade_id, $email, $activate);
        return $uniqueUsername;        
    }
    
    
    public static function generateRandomString($randSeed = 0, $length = self::MIN_PASSWORD_LEN) {
        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ.';
        $charactersLength = strlen($characters);
        srand($randSeed);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /**
     * Make username from name, From Červinka Petr to cervinkap
     * @param string $name "surname name"
     * @return string $username
     */
    public static function name2username($name) {
        $name = Strings::lower(Strings::toAscii(Strings::trim($name)));
        $words = explode(' ',$name);
        if (count($words) == 0) { // jmeno neobsahuje žádné povolene znaky
            return "anonym--".date('Y-m-d--H-i-s').'--'.rand(0,1000);
        }
        else if(count($words) == 1) { // jmeno neobsahuje dve slova
            return Strings::trim(Strings::webalize($name, null, true),'-');
        }
        else { // bezny pripad
            $surname = Strings::trim($words[0], '-');
            $firstname = Strings::trim($words[1], '-');
            return Strings::trim(Strings::webalize($surname . $firstname[0], null, true), '-'); 
        }
        
    }
    
    /**
     * Remove all acount, what do not been actived
     */
    public function removeDeactivated() {
        $a = new Acount($this->db);
        $a->where('activate', 0)->delete();
    }

    
    /**
     * Activate all acount, what do not been actived and generate passwords
     */
    public function activateAll() {
        $acount = new Acount($this->db);
        $acount->where('activate', 0);
        $upd = array();
        foreach($acount->where('activate', 0) as $a) {
            $upd[]=array(
                'id' => $a->id,
                'activate' => 1,
            );
            if ($a->password == '') {
                $upd['password'] = Acount::generateRandomString($a->id);
            }
        }
        foreach ($upd as $u) {
            $acount = new Acount($this->db);
            $acount->where('id', $u['id'])->update($u);
        }
    }
    
}
