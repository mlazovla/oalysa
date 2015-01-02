<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\Topic;
use App\Model\Subject;
use App\Model\Grade;
use App\Model\Comentary;
use App\Model\Attachement;
use App\Model\MyAuthorizator;
use Tester\Runner\CommandLine;


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
        // Neopravneny uzivatel
        if (!$this->user->loggedIn || !$this->user->isAllowed('topic','read')) {
            $this->flashMessage('Nemáte oprávnění číst články.', 'warning');
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
        
        if ($this->user->isAllowed('attachement', 'read')) {   
            /**
             * @var Attachement $attachement
             */
            $attachement = new Attachement($this->database);
            $this->template->attachements = $attachement->getByTopic($topicId);
        }
        
        if ($this->user->isAllowed('comentary', 'read')) {        
            /**
             * @var Comentary $comentary
             */
            $comentary = new Comentary($this->database);
            $this->template->comentaries = $comentary->getByTopic($topicId);
        }
        
        $this->template->isAllowedToWriteComents = $this->user->isAllowed('selfComentary', 'insert');
        $this->template->isAllowedToDeleteSelfComent = $this->user->isAllowed('selfComentary','delete');
        $this->template->isAllowedToDeleteAnyComent = $this->user->isAllowed('comentary','delete');
        
        
    }
    
    public function renderDownloadAttachement($attachementId) {
        if (!$this->user->isAllowed('attachement', 'read')) {
            $this->redirect('Homepage:');
            $this->flashMessage('Nemáte oprávnění číst přílohu článku.','warning');
            return;
        }
        
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
        if (!$this->user->isAllowed('attachement', 'read')) {
            $this->redirect('Homepage:');
            $this->flashMessage('Nemáte oprávnění číst přílohu článku.','warning');
            return;
        }
                    
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
        $values = $form->getValues();
        
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        if (!$this->user->isAllowed('selfComentary', 'insert')) {
            $this->flashMessage('Nemáte oprávnění komentovat články.','warning');
            $this->redirect('show', $values->topic_id);
            return;
        }
        
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
    
        $this->flashMessage("Komentář přidán.", 'success');
        $this->redirect('show', $values->topic_id);
    }
    
    public function actionDeleteComent($coment_id) {
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        $coment = new Comentary($this->database);
        $owner_id = $coment->get($coment_id)->user->id;
        $topic_id = $coment->get($coment_id)->topic->id;
        if ($this->user->id == $owner_id) { // Vlastni komentar
            if (!$this->user->isAllowed('selfComentary', 'delete')) {
                $this->flashMessage('Nemáte oprávnění odstranit vlastní komentář.','warning');
                $this->redirect('show', $topic_id);
            } 
            $coment = new Comentary($this->database);
            $coment->where('id', $coment_id)->delete(); 
            $this->flashMessage('Komentář byl odstraněn.');
            $this->redirect('show', $topic_id);     
        }
        else { // komentar libovolneho uzivatele
            if (!$this->user->isAllowed('comentary', 'delete')) {
                $this->flashMessage('Nemáte oprávnění mazat komentáře.','warning');
                $this->redirect('show', $topic_id);
                return;
            }
            $coment = new Comentary($this->database);
            $coment->where('id', $coment_id)->delete();
            $this->flashMessage('Komentář byl odstraněn.');
            $this->redirect('show', $topic_id);
        }
    }
    
}