<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public function beforeRender()
    {
        $this->template->isLoggedIn = $this->user->isLoggedIn();
        if ($this->user->isLoggedIn()) {
            $currentUser['id'] = $this->user->getIdentity()->getId();
            $currentUser['role'] = $this->user->authenticatedRole;
            $this->template->currentUser = $currentUser;            
        }
    }
    
}
