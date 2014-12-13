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
    
    public function renderDownloadAttachement($attachementId) {
        $attachement = new Attachement($this->database);
 
        $path = $attachement->getPathById($attachementId);
        $filename = $attachement->get($attachementId)->file;
        header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
        header('Last-Modified: ' . $attachement->get($attachementId)->created_at . ' GMT');
        header('Accept-Ranges: bytes');  // Allow support for download resume
        header('Content-Length: ' . filesize($path));  // File size
        header('Content-Encoding: none');
        header('Content-Type: ' . $attachement->get($attachementId)->mimeType);  // Change the mime type if the file is not PDF
        header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
        readfile($path);  // This is necessary in order to get it to actually download the file, otherwise it will be 0Kb
        exit();
    }
    
    public function renderOpenAttachement($attachementId) {
        $attachement = new Attachement($this->database);
 
        $path = $attachement->getPathById($attachementId);
        $filename = $attachement->get($attachementId)->file;
                
        header('Content-type: $attachement->get($attachementId)->mimeType');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($path));
        header('Accept-Ranges: bytes');
        
        @readfile($path);
        exit();
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