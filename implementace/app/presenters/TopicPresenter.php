<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\Topic;
use App\Model\Subject;
use App\Model\Grade;
use App\Model\Comentary;
use App\Model\Attachement;


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
        
        /**
         * @var Comentary $comentary
         */
        $comentary = new Comentary($this->database);
        $this->template->comentaries = $comentary->getByTopic($topicId);

        /**
         * @var Attachement $attachement
         */
        $attachement = new Attachement($this->database);
        $this->template->attachements = $attachement->getByTopic($topicId);
        
        
    }
    
    protected function createComponentComentaryForm()
    {
        $form = new Nette\Application\UI\Form;
        $form->addTextArea('content', 'komentář:')
        ->setRequired();
        $form->addHidden('topic_id', $this->getHttpRequest()->getQuery('topicId'));
        $form->addHidden('answer_on', null);
        $form->addSubmit('send', 'Komentovat');
        $form->onSuccess[] = $this->comentaryFormSucceeded;
    
        return $form;
    }
    
    public function comentaryFormSucceeded($form)
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:');
            return;
        }
        
        $values = $form->getValues();
        $answer_on = (is_numeric($values['answer_on'])) ? $values['answer_on'] : null;
        
        $comentary = new Comentary($this->database);
        $comentary->insert(
            array(
                'user_id' => $this->user->getIdentity()->id,
                'topic_id'=> $values['topic_id'],
                'content' => $values['content'],
                'comentary_id' => $answer_on
            )
        );
    
        $this->flashMessage("Díky za komentář.", 'success');
        $this->redirect('show', $values->topic_id);
    }
    
}