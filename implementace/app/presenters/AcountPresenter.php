<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\User;
use App\Model\MyAuthorizator;
use App\Model\Acount;
use App\Model\Role;
use Nette\Utils\Random;

/**
 * Acount presenter  
 */
class AcountPresenter extends BasePresenter
{
    private $acount;
    const defaultRole = 2;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->acount = new Acount($this->database);
    }
    
	public function renderDefault()
	{   
	    $this->template->user = $this->database->table('User')
	       ->select('id, username, name')
	       ->wherePrimary($this->user->id)->get($this->user->id);
		
	    $isAllowedReadAcount = false;
		if ($this->user->isLoggedIn()) {
		    $isAllowedReadAcount = $this->user->isAllowed('acount', 'read');
		    $this->template->isAllowedReadAcount = $isAllowedReadAcount;
		    $this->template->isAllowedInsertAcount = $this->user->isAllowed('acount', 'insert');
		    $this->template->isAllowedUpdateAcount = $this->user->isAllowed('acount', 'update');
		    $this->template->isAllowedDeleteAcount = $this->user->isAllowed('acount', 'delete');
		    $this->template->isAllowedResetPasswordAcount = $this->user->isAllowed('acount', 'resetPassword');    
		}
		else {
		    $this->redirect('Homepage:');
		}
		
		$this->template->acount = $this->acount->get($this->user->id);
		
		$acounts = array();
		if ($isAllowedReadAcount) {
		    $this->acount = new Acount($this->database);
		    $acounts = $this->acount->order('grade.name, name');
		    $this->template->acounts = $acounts;
		}
	}
		
	public function renderShow($acountId)
	{
	    if (!$this->user->isLoggedIn()) {
	       $this->redirect('Homepage:');
	    }
	    $this->template->user = $this->database->table('User')
	    ->select('id, username, name')
	    ->wherePrimary($this->user->id)->get($this->user->id);
	
        $isAllowedReadAcount = $this->user->isAllowed('acount', 'read');
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
	
	    $this->template->acount = $this->acount->get($acountId);
	
	    $acounts = array();
	    if ($isAllowedReadAcount) {
	        $this->acount = new Acount($this->database);
	        $acounts = $this->acount->order('grade.name, name');
	        $this->template->acounts = $acounts;
	    }
	    
	}

	

	
	protected function createComponentAcountForm()
	{
	    $form = new Nette\Application\UI\Form;
	    $form->addText('name', 'Jméno:')->setRequired();
	    $form->addText('username', 'Uživatelské jméno:')->setRequired();
	    $form->addText('password', 'Heslo:')->setRequired();
	    $form->addText('email', 'Email:'); 
	    
	    $role = new Role($this->database);
	    $role = $role->select('id, name');
	    $roles = array();
	    foreach ($role as $r) {
	        $roles[$r->id] = $r->name;
	    }
	    $form->addSelect('role_id', 'Role:', $roles)->setValue(self::defaultRole);

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

	    $this->acount = new Acount($this->database);
	    $username = $values['username'];
	    $randMax = 9;
	    while(!$this->acount->add($username, $values['password'], $values['role_id'], $values['name'], $values['email'])) {
	        $username = $values['username'] . rand(1,$randMax++);
	    }
	    if ($username == $values['username']) {
	        $this->flashMessage("Účet ". $values->username ." (". $values->name .") přidán.", 'success'); 	         
	    }
	    else {  
	        $this->flashMessage("Zadané uživatelské jméno \"". $values->username ."\" již existuje. ". $values['name'] ." byl uložen pod uživatelským jménem \"$username\".", 'warning');
	         
	    }
	    $this->redirect('Acount:new');

	}
	

}
