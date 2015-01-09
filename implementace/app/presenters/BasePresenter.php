<?php

namespace App\Presenters;

use Nette, App\Model;
use Nette\Database\Context;
use App\Model\MyAuthorizator;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected $database;
    
    public function injectDatabase(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    
    public function beforeRender()
    {
        $this->template->isLoggedIn = $this->user->isLoggedIn();
        if ($this->user->isLoggedIn()) 
        {
            $authorizator = new \App\Model\MyAuthorizator;
            $authorizator->injectDatabase($this->database);
            $this->user->setAuthorizator($authorizator);
             if(!$this->user->isAllowed('login')) {
                $this->user->logout();
                $this->flashMessage('Nemáte oprávnění se přihlásit. Obraťte se na administrátora webu.', 'warning');
             }
            
            $row = $this->database->table('User')->get($this->user->identity->getId());
            $currentUser = $row;
           
            $this->template->currentUser = $currentUser;    
        }
    }
    
    protected function setMyAutorizator() {
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);   
    }
    
}
