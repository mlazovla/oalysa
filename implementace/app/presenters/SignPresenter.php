<?php

namespace App\Presenters;

use Nette,
	App\Model;
use App\Model\Log;
use Nette\DI\Container;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Login:')
			->setRequired('Zadejte své jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte své heslo.');

		$form->addCheckbox('remember', 'Zapamatovat');

		$form->addSubmit('send', 'Přihlásit:');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}


	public function signInFormSucceeded($form, $values)
	{
	    // doba platnosti prihlaseni
		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}
        
		// prihlas uzivatele
		
		try {
			$this->getUser()->login($values->username, $values->password);
			
			$httpRequest = $this->getHttpRequest();
			$this->log = new Log($this->database);
			$this->log->addLogin($this->user->id, $httpRequest->getRemoteAddress());
			$this->redirect('Homepage:'); //uspesne prihlaseni

		} catch (Nette\Security\AuthenticationException $e) { // neuspesne prihlaseni
			$form->addError($e->getMessage());
		}
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen/a.');
		$this->redirect('in');
	}

}
