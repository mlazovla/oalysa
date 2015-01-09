<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\User;
use App\Model\MyAuthorizator;
use App\Model\Acount;

/**
 * Acount presenter  
 */
class AcountPresenter extends BasePresenter
{
    private $acount;
    
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
		
		if ($this->user->isLoggedIn()) {
		    $isAllowedReadAcount = $this->user->isAllowed('acount', 'read');
		    $this->template->isAllowedReadAcount = $isAllowedReadAcount;
		    $this->template->isAllowedInsertAcount = $this->user->isAllowed('acount', 'insert');
		    $this->template->isAllowedUpdateAcount = $this->user->isAllowed('acount', 'update');
		    $this->template->isAllowedDeleteAcount = $this->user->isAllowed('acount', 'delete');
		    $this->template->isAllowedResetPasswordAcount = $this->user->isAllowed('acount', 'resetPassword');    
		}
		
		$this->template->acount = $this->acount->get($this->user->id);
		
		$acounts = array();
		if ($isAllowedReadAcount) {
		    $this->acount = new Acount($this->database);
		    $acounts = $this->acount->order('grade.name, name');
		    $this->template->acounts = $acounts;
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
