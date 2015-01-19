<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\User;
use App\Model\News;
use App\Model\Subject;

use App\Model\MyAuthorizator;
use App\Model\Topic;

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
		    $this->template->news = $news->select('*')->order('created_at DESC')->limit(9);	

		    $subjects = new Subject($this->database);
		    $this->template->subjects = $subjects->select('*')->order('shortcut');
            
		    $this->template->isAllowedInsertNew = $this->user->isAllowed('news', 'insert');
		    $this->template->isAllowedDeleteNew = $this->user->isAllowed('news', 'delete');
		    $this->template->isAllowedInsertSubject = $this->user->isAllowed('subject', 'insert');
		    
		    $topic = new Topic($this->database);
		    $this->template->lastTopics = $topic->order('created_at DESC')->limit(9);
		}
	}
	
	protected function createComponentNewsForm()
	{
	    $form = new Nette\Application\UI\Form;
	    $form->addTextArea('content', 'novinka:')->setRequired();
	    $form->addSubmit('send', 'Přidat');
	    $form->onSuccess[] = $this->newsFormSucceeded;
	
	    return $form;
	}
	
	public function newsFormSucceeded($form)
	{
	    $values = $form->getValues();
	
	    $authorizator = new MyAuthorizator();
	    $authorizator->injectDatabase($this->database);
	    $this->user->setAuthorizator($authorizator);
	    if (!$this->user->isAllowed('news', 'insert')) {
	        $this->flashMessage('Nemáte oprávnění přidávat novinky.','warning');
	        $this->redirect('Homepage:');
	        return;
	    }
	
	    $news = new News($this->database);
	    $news->insert(
	        array(
	            'user_id' => $this->user->getId(),
	            'content' => $values['content'],
	        )
	    );
	
	    $this->flashMessage("Novinka přidána.", 'success');
	    $this->redirect('Homepage:');
	}
	
	public function actionDeleteNews($news_id) {
	    $authorizator = new MyAuthorizator();
	    $authorizator->injectDatabase($this->database);
	    $this->user->setAuthorizator($authorizator);
	     
	    if (!$this->user->isAllowed('news', 'delete')) {
	        $this->flashMessage('Nemáte oprávnění mazat novinky.','warning');
	        $this->redirect('Homepage:');
	        return;
	    }
	    $news = new News($this->database);
	    $news->where('id', $news_id)->delete();
	    $this->flashMessage('Novinka smazána.','success');
	    $this->redirect('Homepage:');  
	}
	

}
