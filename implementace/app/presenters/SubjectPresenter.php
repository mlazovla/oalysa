<?php
namespace App\Presenters;

use Nette,
App\Model;
use App\Model\User;
use App\Model\News;
use App\Model\Subject;
use Latte\Template;
use App\Model\Grade;
use App\Model\Subject2Grade;


/**
 * Subject presenter.
 */
class SubjectPresenter extends BasePresenter
{
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    
    /**
     * Prepare data to display one concrete subject
     * @param int $subjectId 
     */
    public function renderShow($subjectId)
    {
        // Neprihlaseny uzivatel
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:');
            return;
        }
        
        $subject = new Subject($this->database);
        $s2g = new Subject2Grade($this->database);
        
        $this->template->subject = $subject->get($subjectId);
        
        $this->template->grades = $subject->getGrades($subjectId);
        
        
        
        
    }
}
