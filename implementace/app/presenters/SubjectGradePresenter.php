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
    private $database;

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
        // Neprihlaseny uzivatel
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:');
            return;
        }

        $subject = new Subject($this->database);
        $grade = new Grade($this->database);
        $topic = new Topic($this->database);
        
        $this->template->subject = $subject->get($subjectId);
        $this->template->grade = $grade->get($gradeId);
        
        
        if ($gradeId == null) {
            $this->template->topics = null;
        }
        else {
            $this->template->topics = $topic->where('Subject2Grade.grade_id', $gradeId)->where('Subject2Grade.subject_id', $subjectId);
        } 


    }
}