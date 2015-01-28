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
use App\Model\Subject2Grade;

use Nette\Application\BadRequestException;
use App\Model\Log;

/**
 * Topic presenter.
 */
class TopicPresenter extends BasePresenter
{
    private $topic;
    
    //-------------------------------------------------------------------------
    // SHOW TOPIC
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->topic = new Topic($this->database);
        $this->log = new Log($this->database);
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
        $this->topic = $topic->get($topicId);
        
        $this->template->subject = $topic->getSubject($topicId);
        $this->template->grade = $topic->getGrade($topicId);
        $this->template->topic = $this->topic;

        $grade = $topic->getGrade($topicId);
        if ($grade != null)
            $gradeId = $grade->id;
        else
            $gradeId = null;
        

        $this->template->topics = $topic->where('subject2grade_id', $this->topic->subject2grade_id);
        
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
        
        // Opravneni
        $this->template->isAllowedToWriteComents =      $this->user->isAllowed('selfComentary', 'insert') && $this->topic->enableDiscussion;
        $this->template->isAllowedToDeleteSelfComent =  $this->user->isAllowed('selfComentary','delete');
        $this->template->isAllowedToDeleteAnyComent =   $this->user->isAllowed('comentary','delete');
        $this->template->isAllowedToDeleteAnyTopic =    $this->user->isAllowed('topic', 'delete');
        $this->template->isAllowedToDeleteSelfTopic =   $this->user->isAllowed('selfTopic', 'delete');
        $this->template->isAllowedToInsertTopic =       $this->user->isAllowed('topic', 'insert');
        $this->template->isAllowedToUpdateTopic =       $this->user->isAllowed('topic', 'update');
        $this->template->isAllowedToInsertAttachement = $this->user->isAllowed('attachement', 'insert');
        $this->template->isAllowedToUpdateAttachement = $this->user->isAllowed('attachement', 'update');
        $this->template->isAllowedToDeleteAttachement = $this->user->isAllowed('attachement', 'delete');
        
        // Log
        $this->log->addVisit($this->user->id, $topicId);
    }
    
    /**
     * Prepare data to display topic what does not attached to subject and grade
     */
    public function renderZombie()
    {
        // Neopravneny uzivatel
        if (!$this->user->loggedIn || !$this->user->isAllowed('topic','read')) {
            $this->flashMessage('Nemáte oprávnění číst články.', 'warning');
            $this->redirect('Homepage:');
            return;
        }
        
        // Zombie temata
        $this->topic = new Topic($this->database);
        $this->template->topics = $this->topic->where('subject2grade_id', null);
        
        // Opravneni
        $this->template->isAllowedToUpdateTopic = $this->user->isAllowed('topic', 'update');
        $this->template->isAllowedToDeleteAnyTopic = $this->user->isAllowed('topic', 'delete');
        $this->template->isAllowedToDeleteSelfTopic = $this->user->isAllowed('selfTopic', 'delete');
    
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
        $this->topic = new Topic($this->database);
        $this->topic = $this->topic->get($values->topic_id);
        
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        if (!$this->user->isAllowed('selfComentary', 'insert') || !$this->topic->enableDiscussion) {
            $this->flashMessage('Článek nelze komentovat. Buď nemáte dostatečná oprávnění nebo je diskuze zakázána.','warning');
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
        $owner_id = $coment->get($coment_id)->user_id;
        $topic_id = $coment->get($coment_id)->topic_id;
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

    
    //-----------------------------------------------------------------------
    // NEW/EDIT/DELETE TOPIC
    //
    
    public function renderNew($subjectId, $gradeId) {
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        
        if (!$this->user->isAllowed('topic', 'insert')) {
            $this->flashMessage('Nemáte oprávnění psát články.','warning');
            $this->redirect("Homepage:");
        }
        
        $subject = new Subject($this->database);
        $grade = new Grade($this->database);
        
        $this->template->subject = $subject->get($subjectId);
        $this->template->grade = $grade->get($gradeId);
        
        
        
    }

    protected function createComponentTopicForm()
    {
        $form = new Nette\Application\UI\Form;
        
        $form->addText('name', 'nadpis:', 60, 127)->setRequired();        
        $form->addTextArea('anotation', 'anotace:');
        $form->addTextArea('content', 'článek:');
        $form->addHidden('subject_id', $this->getHttpRequest()->getQuery('subjectId'));
        $form->addHidden('grade_id', $this->getHttpRequest()->getQuery('gradeId'));
        $form->addCheckbox('enableDiscussion', 'Povolit pod tématem diskuzi:')->setValue(1);
        $form->addMultiUpload('attachements', 'Přílohy:');      
        $form->addSubmit('send', 'Přidat článek');
        $form->onSuccess[] = $this->topicFormSucceeded;
    
        return $form;
    }
    
    
    public function topicFormSucceeded($form)
    {
        $values = $form->getValues();
    
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        if (!$this->user->isAllowed('topic', 'insert')) {
            $this->flashMessage('Nemáte oprávnění psát články.','warning');
            $this->redirect("Homepage:");
        }
        if (!$this->user->isAllowed('attachement', 'insert')) {
            $this->flashMessage('Nemáte oprávnění přidat přílohu.','warning');
            $this->redirect("Homepage:");
        }
        
        $s2g = new Subject2Grade($this->database);
        $s2g_id = $s2g->getRelationId($values['subject_id'], $values['grade_id']);
                
        $topic = new Topic($this->database);
        $topic->insert(
            array(
                'user_id' => $this->user->getIdentity()->id,
                'anotation'=> $values['anotation'],
                'content'=> $values['content'],
                'name'=> $values['name'],
                'enableDiscussion'=> $values['enableDiscussion'],                
                'content' => $values['content'],
                'subject2grade_id' => $s2g_id
            )
        );
        $lastTopic = $topic->select('id')->order('id DESC')->limit(1)->fetch();  
        
        if (!$s2g_id) {
            $this->flashMessage('Během ukládání došlo k chybě. Článek byl uložen, ale nebyl přiřazen k žádnému předmětu ani ročníku.','error');
        }
        else {
            $this->flashMessage("Článek ". $values['name'] ." byl přidán.", 'success');
        }
        
        $attachement = new Attachement($this->database);
        foreach ($values['attachements'] as $f)
        {            
            if (!$attachement->insertFile($f, $lastTopic['id'], $this->user->id))
                $this->flashMessage('Nepodařilo se uložit přílohu: ' . $f->getName());
        }
        
        
        $this->redirect('show', $lastTopic['id']);
    }
    
    /**
     * Delete topic by id
     * @param int $topic_id
     */
    public function actionDeleteTopic($topic_id) {
        $this->setMyAutorizator();
        
        $this->topic = new Topic($this->database);
        $this->topic = $this->topic->get($topic_id);
        $s2g_id = $this->topic['subject2grade_id'];
        $s2g = new Subject2Grade($this->database);
        $s2g = $s2g->get($s2g_id);     
        $subjectId = (string)$s2g['subject_id'];
        $gradeId = (string)$s2g['grade_id'];

        
        
        $isAllowedToDeleteThis = 
            (
                $this->user->isAllowed('topic', 'delete') || 
                    ($this->user->isAllowed('selfTopic', 'delete') && $this->topic->user_id == $this->user->id)
            ) && 
                $this->user->isAllowed('attachement', 'delete');
            
                
        if (!$isAllowedToDeleteThis) {
            $this->flashMessage('Nemáte oprávnění smazat tento článek.','warning');
            $this->redirect('show', $topic_id);
            return;
        }              
        
        
        
        $topic = new Topic($this->database);
        $topic->safeDelete($topic_id);
        $this->flashMessage('Článek byl odstraněn včetně všech příloh.');
        if ($subjectId && $gradeId) {
            $this->redirect('SubjectGrade:show', array($subjectId, $gradeId));
        }
        else {
            $this->redirect('Topic:zombie');
        }       
    }
    
    /**
     * Form to add Attachement
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentAttachementForm()
    {
        $form = new Nette\Application\UI\Form;
       
        $form->addHidden('topic_id', $this->getHttpRequest()->getQuery('topicId'));
    
        $form->addMultiUpload('attachements', 'Přílohy:');
    
        $form->addSubmit('send', 'Přidat přílohu');
        $form->onSuccess[] = $this->attachementFormSucceeded;
    
        return $form;
    }
    
    /**
     * Solving attachement form
     * @param \Nette\Application\UI\Form $form
     */
    public function attachementFormSucceeded($form)
    {
        $values = $form->getValues();
    
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
        if (!$this->user->isAllowed('attachement', 'insert')) {
            $this->flashMessage('Nemáte oprávnění přidat přílohu.','warning');
            $this->redirect("Topic:show ". $values['topic_id']);
        }
    
        $res = array();
        $attachement = new Attachement($this->database);
        foreach ($values['attachements'] as $f)
        {
            if (!$attachement->insertFile($f, $values['topic_id'], $this->user->id))
                $this->flashMessage('Nepodařilo se uložit přílohu: ' . $f->getName(), 'warning');
            else {
                $res[] = $f->getName();
            }
        }
    
        $message = '';
        if (count($res) != 0) {
            foreach ($res as $r) {
                $message .= $r . ', ';
            }
            $message = substr($message, 0, -2);
            $this->flashMessage('Přílohy: ' . $message .' byly úspěšně nahrány.');
        }
    
        $this->redirect('show', $values['topic_id']);
    }
    
    /**
     * Render update topic
     * @param int $topicId
     */
    public function renderUpdate($topicId) {
        $this->topic = new Topic($this->database);
        $this->topic = $this->topic->where('id', $topicId)->fetch();
        
        $allowed = false;
        if ($this->topic->user_id == $this->user->id) {
            $allowed = $this->user->isAllowed('selfTopic', 'update');
        }
        if (!$allowed) {
            $allowed = $this->user->isAllowed('topic', 'update');
        }

        if (!$allowed) {
            $this->flashMessage('Nemáte oprávnění upravovat tento článek.','warning');
            $this->redirect('Topic:show', $topicId);
            return;
        }
        
        if (!$this->topic) { // kontrola existence záznamu
            throw new BadRequestException;
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
            $this->template->topics = $topic->where('subject2grade_id', $gradeId);
        }
    
        $this['topicUpdateForm']->setDefaults(array(
            'id' => $this->topic->id,
            'name' => $this->topic->name,
            'anotation' => $this->topic->anotation,
            'content' => $this->topic->content,
            'enableDiscussion' => $this->topic->enableDiscussion,
        )); // nastavení výchozích hodnot
    }
    
    /**
     * Form to edit topic TopicUpdate
     */
    protected function createComponentTopicUpdateForm()
    {
    
        $form = new Nette\Application\UI\Form;
        $form->addText('name', 'nadpis:', 60, 127)->setRequired();        
        $form->addTextArea('anotation', 'anotace:');
        $form->addTextArea('content', 'článek:');
        $form->addHidden('id', '');
        $form->addCheckbox('enableDiscussion', 'Povolit pod tématem diskuzi:')->setValue(1);
        $form->addSubmit('send', 'Uložit');
        
        $form->onSuccess[] = callback($this, 'topicUpdateFormSucceeded');
        return $form;
    }
    
    /**
     * Topic update
     * @form topicUpdateForm
     */
    public function topicUpdateFormSucceeded($form)
    {
        // data z formulare
        $values = $form->getValues();
    
        // nacitani z databaze
        $this->topic = new Topic($this->database);
        $this->topic = $this->topic->where('id', $values['id'])->fetch();
        
        // kontrola opravneni
        $authorizator = new MyAuthorizator();
        $authorizator->injectDatabase($this->database);
        $this->user->setAuthorizator($authorizator);
      
        $allowed = false;
           
        if (!$this->topic->user_id || $this->topic->user_id == $this->user->id) { // vlastni clanek
            $allowed = $this->user->isAllowed('selfTopic', 'update');
        }
        if (!$allowed) { // libovolny clanek
            $allowed = $this->user->isAllowed('topic', 'update');
        }
        
        if (!$allowed) { // uzivatel neni opravnen upravovat clanek
            $this->flashMessage('Nemáte oprávnění upravovat tento článek.','warning');
            $this->redirect('Topic:show', $values['id']);
            return;
        }
        
        // kontrola existence zaznamu
        if (!$this->topic) { 
            throw new BadRequestException;
        }
        
        $updateArray = array(
                'name' => $values['name'],
                'anotation' => $values['anotation'],
                'content' => $values['content'],
                'enableDiscussion' => $values['enableDiscussion'],
            );
        if (!$this->topic->user_id) { // článek nemá autora
            $updateArray['user_id'] = $this->user->id; 
        }
        
    
        $this->topic = new Topic($this->database);
        $this->topic->where('id', $values['id'])->update($updateArray);
    
        $this->flashMessage('Článek '. $values['name'] .' byl upraven.', 'success');
        $this->redirect('Topic:show', $values['id']);
    }

    
    /**
     * Attach an topic to Subject and Grade
     * @param int $subjectId
     */
    public function renderAttach($subjectId)
    {
        // Neopravneny uzivatel
        if (!$this->user->loggedIn || !$this->user->isAllowed('topic','update')) {
            $this->flashMessage('Nemáte oprávnění přiřazovat témata.', 'warning');
            $this->redirect('Homepage:');
            return;
        }
    
        $this->topic = new Topic($this->database);
        $this->template->topic = $this->topic->get($subjectId);
        
        $this['attachTopicForm']->setDefaults(array('id'=>$subjectId, 'subject2grade' => $this->template->topic->subject2grade_id));
    }
    
    
    protected function createComponentAttachTopicForm()
    {
        $form = new Nette\Application\UI\Form;
    
        $s2g = new Subject2Grade($this->database);
        $s2g = $s2g->order('subject.shortcut ASC, grade.name ASC');

        $radioList = array();
        foreach($s2g as $r) {
            $radioList[$r->id]= $r->subject->shortcut .' '. $r->grade->name;
        }
        
        $form->addRadioList('subject2grade', 'Přiřadit téma k:', $radioList)->setRequired();
        
        $form->addSubmit('send', 'Přiřadit');

        $form->addHidden('id');

        $form->onSuccess[] = callback($this, 'attachTopicFormSucceeded');
        
        return $form;
    }

    /**
     * Attach topic to Subject and Grade
     * @form attachTopicForm
     */
    public function attachTopicFormSucceeded($form)
    {
        // data z formulare
        $values = $form->getValues();
        
        // nacitani z databaze
        $this->topic = new Topic($this->database);
        $this->topic = $this->topic->where('id', $values['id'])->fetch();
        $s2g = new Subject2Grade($this->database);
        $s2g = $s2g->get($values['subject2grade']);
        
        // kontrola opravneni
        $this->setMyAutorizator();
        
        $allowed = false;
         
        if ($this->topic->user_id == $this->user->id) { // vlastni clanek
            $allowed = $this->user->isAllowed('selfTopic', 'update');
        }
        if (!$allowed) { // libovilny clanek
            $allowed = $this->user->isAllowed('topic', 'update');
        }
        
        if (!$allowed) { // uzivatel neni opravnen upravovat clanek
            $this->flashMessage('Nemáte oprávnění upravovat tento článek.','warning');
            $this->redirect('Topic:show', $values['id']);
            return;
        }
        
        // kontrola existence zaznamu
        if (!$this->topic) {
            throw new BadRequestException('Téma neexistuje.');
        }
        if (!$s2g) {
            throw new BadRequestException('Neexistuje spojení předmět a ročník.');
        }
        
        // zapis zmen
        $this->topic = new Topic($this->database);
        $this->topic->where('id', $values['id'])->update(
            array(
                'subject2grade_id' => $values['subject2grade']
            )
        );
        
        $this->flashMessage('Článek '. $this->topic->name .' byl přiřazen.', 'success');
        $this->redirect('Topic:show', $values['id']);        
    }
    
};    
