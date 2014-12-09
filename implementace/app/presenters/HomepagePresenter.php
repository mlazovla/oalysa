<?php

namespace App\Presenters;

use Nette,
	App\Model;
use App\Model\User;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

    private $database;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    
	public function renderDefault()
	{
	    $this->template->user = $this->database->table('User')
	       ->select('id, username, name')
	       ->wherePrimary($this->user->id)->get($this->user->id);
		
	    $this->template->isLoggedIn = $this->user->isLoggedIn();
		$this->template->subjects = $this->database->table('Subject')
		->order('shortcut ASC');
		
		
	}

}
