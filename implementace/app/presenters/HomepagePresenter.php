<?php

namespace App\Presenters;

use Nette,
    App\Model;
use App\Model\User;
use App\Model\News;
use App\Model\Subject;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    
	public function renderDefault()
	{
	    
	    $this->template->user = $this->database->table('User')
	       ->select('id, username, name')
	       ->wherePrimary($this->user->id)->get($this->user->id);
		
		$this->template->subjects = $this->database->table('Subject')
		->order('shortcut ASC');

		$this->template->news = null;
		$this->template->subjects = null;
		
		if ($this->user->isLoggedIn()) {
		    $news = new News($this->database);
		    $this->template->news = $news->select('*')->order('created_at')->limit(3);	

		    $subjects = new Subject($this->database);
		    $this->template->subjects = $subjects->select('*')->order('shortcut');
		}
		
	   
	}

}
