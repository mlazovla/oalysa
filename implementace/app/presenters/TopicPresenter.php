<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\Topic;
use App\Model\Subject;
use App\Model\Grade;


/**
 * Topic presenter.
 */
class TopicPresenter extends BasePresenter
{

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /**
     * Prepare data to display one Topic (discusion, ...)
     * @param int $subjectId
     */
    public function renderShow($topicId)
    {
        // Neprihlaseny uzivatel
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:');
            return;
        }

        $subject = new Subject($this->database);
        $grade = new Grade($this->database);
        $topic = new Topic($this->database);

        $this->template->subject = $topic->getSubject($topicId);
        $this->template->grade = $topic->getGrade($topicId);
        $this->template->topic = $topic->get($topicId);

        $gradeId = $topic->getGrade($topicId)->id;
        if ($gradeId == null) {
            $this->template->topics = null;
        }
        else {
            $this->template->topics = $topic->where('Subject2Grade.grade_id', $gradeId);
        }


    }
}