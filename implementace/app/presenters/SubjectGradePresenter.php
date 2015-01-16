<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\Topic;
use App\Model\Subject;
use App\Model\Grade;


/**
 * Subject Grade presenter.
 */
class SubjectGradePresenter extends BasePresenter
{

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /**
     * Prepare data to display one concrete subject and grade
     * @param int $subjectId
     */
    public function renderShow($subjectId, $gradeId=null)
    {
        // Neprihlaseny nebo neopravneny uzivatel        
        if (!$this->user->isLoggedIn() || !$this->user->isAllowed('topic', 'read')) {
            $this->flashMessage('Nemáte oprávnění číst články.', 'warning');
            $this->redirect('Homepage:');
            return;
        }
          

        $subject = new Subject($this->database);
        $grade = new Grade($this->database);
        $topic = new Topic($this->database);
        
        $this->template->subject = $subject->get($subjectId);
        $this->template->grade = $grade->get($gradeId);
        
        $isAllowedToEditTopic = $this->user->isAllowed('topic', 'insert');
        $this->template->isAllowedToEditTopic = $isAllowedToEditTopic;
        $this->template->isAllowedToDeleteAnyTopic = $this->user->isAllowed('topic', 'delete');
        $this->template->isAllowedToDeleteSelfTopic = $this->user->isAllowed('selfTopic', 'delete');
        
        $notAssignedTopicCount = 0;
        if ($isAllowedToEditTopic) {
            $this->template->notAssignedTopicCount = $topic->getZombieCount();
        }    
        
        if ($gradeId == null) {
            $this->template->topics = null;
        }
        else {
            $this->template->topics = $topic->where('Subject2Grade.grade_id', $gradeId)->where('Subject2Grade.subject_id', $subjectId);
        } 

    }
}