<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\User;
use App\Model\MyAuthorizator;
use App\Model\Acount;
use App\Model\Role;
use Nette\Utils\Random;
use App\Model\Grade;
use Nette\Application\UI\Form;
use App\Model\UserManager;
use Nette\Application\BadRequestException;
use Nette\Utils\Strings;
use Nette\Application\ForbiddenRequestException;
use App\Model\Log;
/**
 * Acount presenter  
 */
class AcountPresenter extends BasePresenter
{
    private $acount;
    
    /**
     * Default role ID in forms
     * @var int
     */
    const defaultRole = 2;
    const EREG_PATTERN_USERNAME = '/([0-9a-z]|[\-])+/';
    const EREG_PATTERN_PASSWORD = '/(.{5}.*)/'; // Acount::MIN_PASSWORD_LEN
    const EREG_PATTERN_EMAIL = '/([a-zA-Z0-9\._\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+)/';
    
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->acount = new Acount($this->database);
        $this->log = new Log($this->database);
    }
    
	public function renderDefault()
	{   
	    $acount = new Acount($this->database);
	    $this->template->user = $acount->get($this->user->id);
	     
	    $isAllowedReadAcount = false;
		if ($this->user->isLoggedIn()) {
		    $isAllowedReadAcount = $this->user->isAllowed('acount', 'read');
		    $this->template->isAllowedReadAcount = $isAllowedReadAcount;
		    $this->template->isAllowedInsertAcount = $this->user->isAllowed('acount', 'insert');
		    $this->template->isAllowedUpdateAcount = $this->user->isAllowed('acount', 'update');
		    $this->template->isAllowedDeleteAcount = $this->user->isAllowed('acount', 'delete');
		    $this->template->isAllowedResetPasswordAcount = $this->user->isAllowed('acount', 'resetPassword');
		    $this->template->isAllowedShowInitPasswordAcount = $this->user->isAllowed('acount', 'showInitPassword');
		}
		else {
		    $this->redirect('Homepage:');
		}
		
		$this->template->acount = $this->acount->get($this->user->id);
		
		$acounts = array();
		if ($isAllowedReadAcount) {
		    $this->acount = new Acount($this->database);
		    $acounts = $this->acount->where('activate', '1')->order('grade.name, name');
		    $this->template->acounts = $acounts;
		}
		$this->template->lastLogin = $this->log->getLastLoginOfUser($this->user->id);
	}
	
	public function renderShow($acountId)
	{
	    if (!$this->user->isLoggedIn()) {
	       $this->redirect('Homepage:');
	    }
	    if ($this->user->id == $acountId) { // pokud chci zobrazit svuj ucet preskoci na renderDefault
	        $this->redirect('Acount:');
	    }
	    $acount = new Acount($this->database);
	    $this->template->user = $acount->get($this->user->id);
	    	
        $isAllowedReadAcount = ($this->user->isAllowed('acount', 'read') || $acountId == $this->user->id);
        if (!$isAllowedReadAcount) {
            $this->flashMessage('Nemáte oprávnění si prohlížet cizí účty.','warning');
            $this->redirect('Acount:');
            return;
        }
        
        $this->template->isAllowedReadAcount = $isAllowedReadAcount;
        $this->template->isAllowedInsertAcount = $this->user->isAllowed('acount', 'insert');
        $this->template->isAllowedUpdateAcount = $this->user->isAllowed('acount', 'update');
        $this->template->isAllowedDeleteAcount = $this->user->isAllowed('acount', 'delete');
        $this->template->isAllowedResetPasswordAcount = $this->user->isAllowed('acount', 'resetPassword');
        $this->template->isAllowedShowInitPasswordAcount = $this->user->isAllowed('acount', 'showInitPassword');
	   
        $this->acount = $this->acount->get($acountId);
        
	    if (!$this->acount) {
	        throw new BadRequestException('Účet neexistuje.');
	    }
	    
	    $this->template->acount = $this->acount;
	     	     
	    $acounts = array();
	    if ($isAllowedReadAcount) {
	        $this->acount = new Acount($this->database);
	        $acounts = $this->acount->where('activate', '1')->order('grade.name, name');
	        $this->template->acounts = $acounts;
	    }
	    $this->template->lastLogin = $this->log->getLastLoginOfUser($acountId);
	    
	}

	public function renderUpdate($acountId)
	{
	    if (!$this->user->isLoggedIn()) {
	        $this->redirect('Homepage:');
	    }
	    $acount = new Acount($this->database);
	    $this->template->user = $acount->get($this->user->id);
	    	
	    $isAllowedUpdateAcount = ($this->user->isAllowed('acount', 'update') || $acountId == $this->user->id);
	    if (!$isAllowedUpdateAcount) {
	        $this->flashMessage('Nemáte oprávnění upravovat cizí účty.','warning');
	        $this->redirect('Acount:');
	        return;
	    }
	    $this->acount = new Acount($this->database);
	    $this->acount = $this->acount->get($acountId);
	    $this->template->acount = $this->acount;
	    $this['updateAcountForm']->setDefaults($this->acount);
	    $this['updateAcountForm']->setDefaults(array('password' => ''));
	}
	
	public function renderBatch()
	{
	    $acount = new Acount($this->database);
	    $this->template->user = $acount->get($this->user->id);
	    	
	    $isAllowedReadAcount = false;
	    if ($this->user->isLoggedIn()) {
	        $isAllowedReadAcount = $this->user->isAllowed('acount', 'read');
	        $this->template->isAllowedReadAcount = $isAllowedReadAcount;
	        $this->template->isAllowedInsertAcount = $this->user->isAllowed('acount', 'insert');
	        $this->template->isAllowedUpdateAcount = $this->user->isAllowed('acount', 'update');
	        $this->template->isAllowedDeleteAcount = $this->user->isAllowed('acount', 'delete');
	        $this->template->isAllowedResetPasswordAcount = $this->user->isAllowed('acount', 'resetPassword');
	        $this->template->isAllowedShowInitPasswordAcount = $this->user->isAllowed('acount', 'showInitPassword');
	    }
	    else {
	        $this->redirect('Homepage:');
	    }
	
	    $this->template->acount = $this->acount->get($this->user->id);
	
	    $acounts = array();
	    if ($isAllowedReadAcount) {
	        $this->acount = new Acount($this->database);
	        $acounts = $this->acount->where('activate', '0')->order('grade.name, name');
	        $this->template->acounts = $acounts;
	    }
	}
	
	public function actionActivateAcounts() {
	    $this->setMyAutorizator();
	    if (!$this->user->isAllowed('acount', 'new')) {
	        throw new ForbiddenRequestException('Nemáte oprávnění aktivovat účty.');
	        return;
	    }
        $this->acount->activateAll();
        $this->flashMessage('Účty byly aktivovány.','success');
	    $this->redirect('Acount:batch');
	}
	
	public function actionRemoveDeactivatedAcounts() {
	    $this->setMyAutorizator();
	    if (!$this->user->isAllowed('acount', 'new')) {
	        throw new ForbiddenRequestException('Nemáte oprávnění aktivovat účty.');
	        return;
	    }
	    $this->acount->removeDeactivated();
	    $this->flashMessage('Poslední dávka byla smazána.','success');
	    $this->redirect('Acount:batch');
	}
	
		
	protected function createComponentAcountForm()
	{
	    $form = new Nette\Application\UI\Form;
	    $form->addText('name', 'Jméno:')->setRequired();
	    $form->addText('username', 'Uživatelské jméno:')
	       ->addRule(Form::PATTERN, 'Uživatelské jméno se smí skládat pouze z malých písmen, čísel a pomlčky.', '([0-9a-z]|[\-])+')
	       ->setRequired('Uživatelské jméno nesmí zůstat prázdné.');
	    $form->addText('password', 'Heslo:')
	       ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', Acount::MIN_PASSWORD_LEN)
	       ->setRequired('Heslo nesmí zůstat prázdné.');
	    $form->addText('email', 'Email:')
	       ->addRule(Form::PATTERN, 'Email nemá správný tvar.', '([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+)?'); 
	    
	    $role = new Role($this->database);
	    $role = $role->select('id, name')->where('assignable', '1');
	    $roles = array();
	    foreach ($role as $r) {
	        $roles[$r->id] = $r->name;
	    }
	    $form->addSelect('role_id', 'Role:', $roles)->setValue(self::defaultRole);

	    $grade = new Grade($this->database);
	    $grade = $grade->select('id, name');
	    $grades = array();
	    $grades[0] = 'Ročník nezvolen';
	    foreach ($grade as $g) {
	        $grades[$g->id] = $g->name;
	    }
	    $form->addSelect('grade_id', 'Ročník:', $grades)->setValue(0);
	    
	    $form->addSubmit('send', 'Založit účet')->setValue('send');
	     
	    $form->onSuccess[] = $this->acountFormSucceeded;
	     
	    return $form;
	}
	
	public function acountFormSucceeded($form)
	{
	    $values = $form->getValues();
	    $this->setMyAutorizator();
	    
	    if (!$this->user->isAllowed('acount', 'insert')) {
	        $this->flashMessage('Nemáte oprávnění přidávat uživatelské účty.','warning');
	        $this->redirect('Acount:');
	        return;
	    }
        if ($values['grade_id'] == 0) $values['grade_id'] = null; // nezvoleny rocnik
	    
	    $this->acount = new Acount($this->database);
        $username = $this->acount->add($values['username'], $values['password'], $values['role_id'], $values['name'], $values['grade_id'], $values['email'], 1, true);
	    if ($username == $values['username']) {
	        $this->flashMessage("Účet ". $values->username ." (". $values->name .") přidán.", 'success'); 	         
	    }
	    else {  
	        $this->flashMessage("Zadané uživatelské jméno \"". $values->username ."\" již existuje. ". $values['name'] ." byl uložen pod uživatelským jménem \"$username\".", 'warning');
	         
	    }
	    $this->redirect('Acount:new');
	}
	
	protected function createComponentUpdateAcountForm()
	{
	    $this->setMyAutorizator();
	    
	    $form = new Nette\Application\UI\Form;
	    $form->addText('name', 'Jméno:')->setRequired()->setDisabled(!$this->user->isAllowed('acount', 'update'));
	    $form->addText('username', 'Uživatelské jméno:')->setRequired()->setDisabled();
	    $form->addCheckbox('changePassword', 'Změnit heslo')->setValue(0);
	    $form->addText('password', 'Nové heslo:')->setValue('')
	       ->addRule(Form::PATTERN, 'Heslo musí mít aplespoň '. Acount::MIN_PASSWORD_LEN .' znaků.', '(.{'. Acount::MIN_PASSWORD_LEN .'}.*)?');
	    
	    $form->addText('email', 'Email:')
	       ->addRule(Form::PATTERN, 'Email nemá správný tvar.', '([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+)?');
	    $form->addHidden('id', 0);   
	    
	    // Role
	    $role = new Role($this->database);
	    $role = $role->select('id, name');
	    $roles = array();
	    foreach ($role as $r) {
	        $roles[$r->id] = $r->name;
	    }
	    $form->addSelect('role_id', 'Role:', $roles)->setDisabled();
	   
	    // Grade
	    $grade = new Grade($this->database);
	    $grade = $grade->select('id, name');
	    $grades = array();
	    $grades[0] = 'Ročník nezvolen';
	    foreach ($grade as $g) {
	        $grades[$g->id] = $g->name;
	    }
	    $form->addSelect('grade_id', 'Ročník:', $grades)->setDisabled(!$this->user->isAllowed('acount', 'update'))->setValue(0);
	     
	    $form->addSubmit('send', 'Upravit účet')->setValue('send');
	
	    $form->onSuccess[] = $this->updateAcountFormSucceeded;
	
	    return $form;
	}
	
	public function updateAcountFormSucceeded($form)
	{
	    $values = $form->getValues();
	    $this->setMyAutorizator();
	     
	    if (!($this->user->isAllowed('acount', 'update') || $values['id'] == $this->user->id)) {
	        $this->flashMessage('Nemáte oprávnění upravovat uživatelské účty.','warning');
	        $this->redirect('Acount:');
	        return;
	    }

	    if (isset($values['grade_id']) && $values['grade_id'] == 0) $values['grade_id'] = null; // nezvoleny rocnik
	    
	    /**
	     * Array to update acount
         * @var array $updateValues
	     */
	    $updateValues = array( 
	        'email' => $values['email']
	    );
	    
	    $message = '';
	    if (isset($values['name'])) {
	        $message .= 'Účet se jménem '.$values['name'].' byl upraven.' ;
	    }
	    else {
	        $message .= 'Účet byl upraven.' ;
	    }
	    
	    if ($values['changePassword'] && strlen($values['password']) >= Acount::MIN_PASSWORD_LEN) {
	        $updateValues['password'] = UserManager::hashPassword($values['password']);
	        $message .= ' Heslo bylo změněno.';
	    }
	    if ($this->user->isAllowed('acount', 'update')) { // Uživatele si nemohou změnit ročník a jméno sami.
	        $updateValues['name'] = $values['name'];
	        $updateValues['grade_id'] = $values['grade_id'];
	    }
	    
	    $this->acount = new Acount($this->database);
        $this->acount->where('id', $values['id'])->update($updateValues);
        $this->flashMessage($message);
        $this->redirect('Acount:show', $values['id']);
	}
	
	public function actionDelete($acountId) {
	    $this->setMyAutorizator();
	    if (!$this->user->isAllowed('acount', 'delete')) {
	        $this->flashMessage('Nemáte oprávnění mazat uživatelské účty.','warning');
	        $this->redirect('Acount:');
	        return;
	    }
	    if ($acountId == $this->user->id) {
	        $this->flashMessage('Nemůžete smazat svůj vlastní účet.','warning');
	        $this->redirect('Acount:');
	        return;
	    }
	    $this->acount = new Acount($this->database);
	    $acount = $this->acount->where('id', $acountId)->fetch();    
	    if (!$acount) {
	        throw new BadRequestException('Účet neexistuje.');
	    }    
	    $this->acount = new Acount($this->database);
	    $this->acount->where('id', $acountId)->delete();
        $this->flashMessage('Účet '. $acount['username'] .' byl smazán.', 'success');
        $this->redirect('Acount:');
	}
	
	public function actionResetPassword($acountId) {
	    $this->setMyAutorizator();
	    if (!$this->user->isAllowed('acount', 'resetPassword')) {
	        $this->flashMessage('Nemáte oprávnění resetovat hesla.','warning');
	        $this->redirect('Acount:');
	        return;
	    }
	    $this->acount = new Acount($this->database);
	    $acount = $this->acount->where('id', $acountId)->fetch();
	    if (!$acount) {
	        throw new BadRequestException('Účet neexistuje.');
	    }
	    $password = Acount::generateRandomString($acountId);
	    $this->acount = new Acount($this->database);
	    $this->acount->where('id', $acountId)->update(array(
            'password' => UserManager::hashPassword($password)    
	    ));
	    $this->flashMessage('Účet '. $acount['username'] .' má vyresetované heslo. Heslo zní "'.$password.'".', 'success');
	    $this->redirect('Acount:');
	}

	public function actionShowInitPassword($acountId) {
	    $this->setMyAutorizator();
	    if (!($this->user->isAllowed('acount', 'showInitPassword') || $acountId == $this->user->id)) {
	        echo "Nemáte oprávnění.";
	        $this->terminate();
	    }
	    echo Acount::generateRandomString($acountId);
	    $this->terminate();
	}
	
	public function renderShowAllInitialPasswords($all = null){
	    if (!$this->user->isAllowed('acount', 'showInitPassword')) {
	        throw new ForbiddenRequestException();
	    }
	    
	    $this->acount = $this->acount
	       ->order('grade.name ASC, name ASC');
	    
	    if ($all) {
	        $this->template->message = "Výpis je kompletní.";
	    }
	    else {
	        $this->acount = $this->acount->where('role_id', self::defaultRole);
	        $this->template->message = "Výpis obsahuje jen žákovské účty.";	         
	    }
	    
	    $acounts = array();
	    foreach($this->acount as $a) {
	        $t['name'] = $a->name;
	        $t['username'] = $a->username;
	        $t['initialPassword'] = Acount::generatePasswordFromId($a->id);
	        $t['gradeName'] = ($a->grade_id) ? ($a->grade->name) : "-";
	        $acounts[] = $t;
	    }
	    $this->template->acounts = $acounts;
	}
	
	protected function createComponentBatchForm()
	{
	    $form = new Nette\Application\UI\Form;
        
	    // k zaškrtnutní
	    $form->addCheckbox('enableAutomaticUsername', 'Automaticky vytvářet z jména uživatelská jména.');
	    $form->addCheckbox('enableAutomaticPassword', 'Automaticky vytvářet hesla.');
	    $form->addCheckbox('enableEmail', 'Vložená data obsahují email.');
	    
	    $role = new Role($this->database);
	    $role = $role->select('id, name')->where('assignable', '1');
	    $roles = array();
	    foreach ($role as $r) {
	        $roles[$r->id] = $r->name;
	    }
	    $form->addSelect('role_id', 'Role:', $roles)->setValue(self::defaultRole);
	    
	    $grade = new Grade($this->database);
	    $grade = $grade->select('id, name');
	    $grades = array();
	    $grades[0] = 'Ročník nezvolen';
	    foreach ($grade as $g) {
	        $grades[$g->id] = $g->name;
	    }
	    $form->addSelect('grade_id', 'Ročník:', $grades)->setValue(0);
	    
	    $form->addText('delimeter', 'Oddělovač záznamů na řádku.', 1, 1)->setValue(';')
	       ->addRule(Form::LENGTH, 'Oddělovač je jen jediný znak, zpravidla čárka nebo středník.', 1);
	     
	    $form->addTextArea('batch', 'Dávka:')->setRequired()->setValue("
	        Vložte zvolená data, každý účet na jeden řádek ve tvaru. \n
	        Uziv-jm;Příjmení Jméno tituly;heslo;email\n
	        \n
	        Bylo-li zvoleno automatické uživatelské jméno, sloupec vůbec neexistuje.\n
	        Bylo-li zvoleno automatické heslo, sloupec vůbec nepište.
	        Pokud u některého účtu nebude heslo odpovídat kritériím, bude nahrazeno počátečním heslem.\n
	        Jsou-li aktivovány emaily, musí sloupec existovat. Neplatné emaily nebudou uživatelům přiřazeny.\n
	        Po kliknutí na Přidat k dávce se připraví uživatelské účty. Nebudete-li spokojen/a,
	        můžete dávku smazat nebo ještě upravit před jejím zapsáním.\n
	        Nepřidávejte stejné lidi dvakrát.\n
	        ");
	    
        $form->addSubmit('send', 'Přidat k dávce')->setValue('send');
	
        $form->onValidate[] = $this->validateBatchForm; 
	    $form->onSuccess[] = $this->batchFormSucceeded;
	
	    return $form;
	}
	
	public function validateBatchForm($form)
	{
	    $values = $form->getValues();
	    	    
	    $lines = explode("\n", $values['batch']);
	    
	    $i = 0;
	    foreach ($lines as $l) {
	        $i++;
	        $cells = explode($values['delimeter'], $l);
	        
	        $usernameOK = (bool)$values['enableAutomaticUsername'];
	        $nameOK = false;
	        $passwordOK = (bool)$values['enableAutomaticPassword'];
	        $emailOK = !(bool)$values['enableEmail'];
	    
	        $width = 3;
	        if ($values['enableAutomaticUsername']) $width--;
	        if ($values['enableAutomaticPassword']) $width--;
	        if ($values['enableEmail']) $width++;
	         
	        
	        $j = 0;
	        foreach ($cells as $c) {
	            $j++;
	            if (!$usernameOK) {
	                if (!(bool)Strings::match(Strings::trim($c), self::EREG_PATTERN_USERNAME))
	                    $form->addError($i . ': je uživatelské jméno v nesprávném tvaru.');
	                $usernameOK = true;
	            }
	        	else if (!$nameOK) {
	                $nameOK = true;
	            }
	            else if (!$passwordOK) {
	                if (!(Strings::match(Strings::trim($c), self::EREG_PATTERN_PASSWORD) || Strings::trim($c) == ''))
	                    $form->addError($i . ': Heslo v nesprávném tvaru.');
	                $passwordOK = true;
	            }
	            else if (!$emailOK) {
	                if (!(Strings::match(Strings::trim($c), self::EREG_PATTERN_EMAIL) || Strings::trim($c) == ''))
	                    $form->addError($i . ': Email v nesprávném tvaru.');
	                $emailOK = true;
	            }
	        }
	        if ($j != $width) {
	            $form->addError($i . ': Nesprávný počet položek na řádku. Očekávaný '.$width.', napočítaný '.$j.'.');
	        }
	    }
	}
	
	public function batchFormSucceeded($form)
	{	     
	    // získani dat z formulare
	    $values = $form->getValues();
	    
	    // opravneni
	    $this->setMyAutorizator(); 
	    if (!$this->user->isAllowed('acount', 'insert')) {
	        $this->flashMessage('Nemáte oprávnění přidávat uživatelské účty, vaše práce je navždy ztracena, není zač.','warning');
	        $this->redirect('Acount:');
	        return;
	    }
	     
	    if ($values['grade_id'] == 0) $values['grade_id'] = null; // nezvoleny rocnik
	    
	    $lines = explode("\n", $values['batch']);
	    foreach ($lines as $l) {
	       $cells = explode($values['delimeter'], $l);

	       $usernameOK = (bool)$values['enableAutomaticUsername'];
	       $nameOK = false;
	       $passwordOK = (bool)$values['enableAutomaticPassword'];
	       $emailOK = !(bool)$values['enableEmail'];

	       $error = false;
	       
	       $res = array();
	       foreach ($cells as $c) {
	           if (!$usernameOK) {
	               $error = !(bool)Strings::match(Strings::trim($c), self::EREG_PATTERN_USERNAME);
	               $res['username'] = $c;
	               $usernameOK = true;
	           }
	           else if (!$nameOK) {
	               $res['name'] = $c;
	               $nameOK = true;
	           }
	           else if (!$passwordOK) {
	               $res['password'] = (bool)Strings::match(Strings::trim($c), self::EREG_PATTERN_PASSWORD) ? $c : '';
	               $passwordOK = true;
	           }
	           else if (!$emailOK) {
	               $res['email'] = (bool)Strings::match(Strings::trim($c), self::EREG_PATTERN_EMAIL) ? $c : '';
	               $emailOK = true;
	           }

	       }   
	       if (!$error) {
	           if (!$values['enableEmail']) {
	               $res['email'] = '';
	           }
	       	   if ($values['enableAutomaticPassword']) {
	               $res['password'] = '';
	           }
               if ($values['enableAutomaticUsername']) {
	               $res['username'] = Acount::name2username($res['name']);
	           }
	           $this->acount->add($res['username'], $res['password'], $values['role_id'], $res['name'], $values['grade_id'], $res['email'], 0);
	       }
	       
	    }
	    
	}

	public function actionMove($type) {
	    $this->setMyAutorizator();
	    if (!$this->user->isAllowed('acount', 'update')) {
	        $this->flashMessage('Nemáte oprávnění upravovat uživatelské účty.','warning');
	        redirect('Acount:');
	        return;
	    }
	    
	    if(($type == 'toBatch' || $type == 'delete') && !$this->user->isAllowed('acount', 'delete')) {
	        $this->flashMessage('Nemáte oprávnění upravovat uživatelské účtys následným mazáním.','warning');
	        redirect('Acount:');
	        return;
	    }
	    $this->acount = new Acount($this->database);
	    if ($this->acount->moveGrade($type)) {
	        $this->flashMessage('Uživatelské učty byly hromadně upraveny.','success');
	        redirect('Acount:');
	    }
	}
	
	
	
	
	

};
