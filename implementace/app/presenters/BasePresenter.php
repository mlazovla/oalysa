<?php

namespace App\Presenters;

use Nette, App\Model;
use Nette\Database\Context;


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
            $row = $this->database->table('User')->get($this->user->identity->getId());
            $currentUser = $row;
           
            $this->template->currentUser = $currentUser;            
        }
    }
    
}
