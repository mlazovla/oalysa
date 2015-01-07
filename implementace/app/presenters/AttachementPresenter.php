<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\Attachement;
use App\Model\Topic;
use App\Model\Subject;
use App\Model\Grade;
use App\Model\MyAuthorizator;


use Nette\Application\BadRequestException;


/**
 * Attachement presenter.
 */
class AttachementPresenter extends BasePresenter
{
    private $attachement;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /**
     * Render update page
     * @param int $attachementId
     */
    public function renderUpdate($attachementId) {
        $this->attachement = new Attachement($this->database);
        $this->attachement = $this->attachement->where('id', $attachementId)->fetch();
                
        if (!$this->user->isAllowed('attachement', 'update')) {
            $this->flashMessage('Nemáte oprávnění upravovat přílohy.','warning');
            $this->redirect('Topic:show', $this->attachement['topic_id']);
            return;
        }

        if (!$this->attachement) { // kontrola existence záznamu
            throw new BadRequestException;
        }
        
        $subject = new Subject($this->database);
        $grade = new Grade($this->database);
        $topic = new Topic($this->database);
        
        $topicId = $this->attachement['topic_id'];
        $this->template->subject = $topic->getSubject($topicId);
        $this->template->grade = $topic->getGrade($topicId);
        $this->template->topic = $topic->get($topicId);
        $this->template->attachement = $this->attachement;
        
        $gradeId = $topic->getGrade($topicId)->id;
        if ($gradeId == null) {
            $this->template->topics = null;
        }
        else {
            $this->template->topics = $topic->where('subject2grade_id', $gradeId);
        }
                
        $this['attachementUpdateForm']->setDefaults(array(
            'attachement_id' => $this->attachement->id,
            'name' => Attachement::getNameWithoutExtension($this->attachement->name),
            'description' => $this->attachement->description,       
        )); // nastavení výchozích hodnot
    }
    
    /**
     * Form to edit Attachement AttachementUpdate
     */
    protected function createComponentAttachementUpdateForm()
    {
        
        $form = new Nette\Application\UI\Form;
        $form->addText('name', 'Jméno:', 20, 120)->setRequired()->addRule(Nette\Application\UI\Form::FILLED, 'Vložte jméno souboru bez koncovky.');
        $form->addTextarea('description', 'Popis:');
        $form->addSubmit('send', 'Uložit');
        $form->addHidden('attachement_id', $this->getHttpRequest()->getQuery('attachementId'));
        $form->onSuccess[] = callback($this, 'attachementUpdateFormSucceeded');
        return $form;
    }
    
    /**
     * Attachement update
     * @form attachementForm
     */
    public function attachementUpdateFormSucceeded($form)
    {
        $values = $form->getValues();
        
        $this->attachement = new Attachement($this->database);
        $this->attachement = $this->attachement->where('id', $values['attachement_id'])->fetch();
        
        $topic_id = $this->attachement->topic_id;
        $ext = $this->attachement->extension;
        
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        if (!$this->user->isAllowed('attachement', 'update')) {
            $this->flashMessage('Nemáte oprávnění upravit přílohu.','warning');
            $this->redirect('Topic:show', $topic_id);
        }
        
        if (!$this->attachement) { // kontrola existence záznamu
            throw new BadRequestException;
        }
        
        if (!Attachement::checkFilename($values['name'])) {
            $this->flashMessage('Neplatné jméno, .','warning');
            $this->redirect('Attachement:update', $values['attachement_id']);
        }
        
        $this->attachement = new Attachement($this->database);
        $this->attachement->where('id',$values['attachement_id'])->update(
            array(
                'description' => $values['description'],
                'name' => trim($values['name']) . '.' . $ext
               )
            );
    
        $this->flashMessage('Příloha '. $this->attachement->name .' byla upravena.', 'success');
        $this->redirect('Topic:show', $topic_id);
    }
    
    
    /**
     * Download an attachement by id
     * @param int $attachementId
     */
    public function renderDownload($attachementId) {
        if (!$this->user->isAllowed('attachement', 'read')) {
            $this->flashMessage('Nemáte oprávnění číst přílohu článku.','warning');
            $this->redirect('Homepage:');
            return;
        }
    
        $attachement = new Attachement($this->database);
    
        $path = $attachement->getPathById($attachementId);
        $filename = $attachement->get($attachementId)->name;
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
    
    
    /**
     * Open an attachement by id in broswer
     * @param int $attachementId
     */
    public function renderOpen($attachementId) {
        if (!$this->user->isAllowed('attachement', 'read')) {
            $this->redirect('Homepage:');
            $this->flashMessage('Nemáte oprávnění číst přílohu článku.','warning');
            return;
        }
    
        $attachement = new Attachement($this->database);
    
        $path = $attachement->getPathById($attachementId);
        $filename = $attachement->get($attachementId)->name;
    
        header('Content-type:' . $attachement->get($attachementId)->mimeType);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($path));
        header('Accept-Ranges: bytes');
    
        @readfile($path);
        exit();
    }
    
    /**
     * Delete an attachement by id
     * @param int $attachementId
     */
    public function actionDelete($attachementId) {
        $attachement = new Attachement($this->database);
        $a = $attachement->where('id', $attachementId)->fetch();
        
        $authorizator = new \App\Model\MyAuthorizator;
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        if (!$this->user->isAllowed('attachement', 'delete')) {
            $this->redirect('Topic:show ', $a->topic_id);
            $this->flashMessage('Nemáte oprávnění smazat přílohu článku.','warning');
            return;
        }
        $attachement->safeDelete($attachementId);
        $this->flashMessage('Příloha '. $a['name'] .' smazána.');
        $this->redirect('Topic:show', $a['topic_id']);
        
    }
    

    
}