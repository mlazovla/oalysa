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
use Nette\Forms\Form;
use Nette\Neon\Exception;
use Nette\Security\AuthenticationException;
use App\Model\Topic;


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
        
        if(!$this->user->isAllowed('subject', 'read')) {
            $this->flashMessage('Nemáte oprávnění prohlížet předměty.','warning');
            $this->redirect('Homepage:');
        }

        $isAllowedToEditTopic = $this->user->isAllowed('topic', 'update');
        $this->template->isAllowedToEditTopic = $isAllowedToEditTopic;
        $this->template->isAllowedToDeleteSubject = $this->user->isAllowed('subject', 'delete');
        $this->template->isAllowedToUpdateSubject = $this->user->isAllowed('subject', 'update');       
        
        $subject = new Subject($this->database);
        $s2g = new Subject2Grade($this->database);
        
        $this->template->subject = $subject->get($subjectId);
        
        $this->template->grades = $subject->getGrades($subjectId);
        
        $topic = new Topic($this->database);
        $notAssignedTopicCount = 0;
        if ($isAllowedToEditTopic) {
            $this->template->notAssignedTopicCount = $topic->getZombieCount();
        }
        

    }
    
        
    protected function createComponentSubjectForm()
    {
        $form = new Nette\Application\UI\Form;
    
        $form->addText('shortcut', 'Zkratka:', 5, 15)
            ->setRequired()
            ->addRule(Form::LENGTH, 'Zkratka musí mít alespoň dva znaky.', array(2,15));
         
        $form->addText('name', 'Celé jméno:', null, 127)
        ->setRequired()
        ->addRule(Form::MAX_LENGTH, 'Příliš dlouhé jméno předmětu.', 127);
    
        $form->addTextArea('description', 'Popis:');
         
        $grade = new Grade($this->database);
        $grade = $grade->select('id, name');
        $grades = array();
        foreach ($grade as $g) {
            $grades[$g->id] = $g->name;
        }
        $form->addCheckboxList('grades', 'Přižazené ročníky', $grades);
         
        $form->addSubmit('send', 'Provést')->setValue('send');
    
        $form->onValidate[] = $this->validateSubjectForm;
        $form->onSuccess[] = $this->subjectFormSucceeded;
    
        return $form;
    }

    protected function createComponentSubjectUpdateForm()
    {
        $form = $this->createComponentSubjectForm();
        $form->addHidden('id');
        
        
        $form->onValidate = array($this->validateSubjectUpdateForm);
        $form->onSuccess = array($this->subjectUpdateFormSucceeded);
        
        return $form;
    }
    
    public function validateSubjectForm($form)
    {
        $values = $form->getValues();
         
        $subject = new Subject($this->database);
         
        // Unikatni zkratka
        if ($subject->where('shortcut', $values['shortcut'])->count('id') > 0) {
            $form->addError('Uvedená zkratka v systému již existuje.');
        }
         
        // Prirazeny rocnik
        $assignedGrade = false;
        foreach ($values['grades'] as $g) {
            if ($g) $assignedGrade = true;
        }
        if (!$assignedGrade) {
            $form->addError('Předmět musí mít přiřazený alespoň jeden ročník.');
        }
    }

    public function validateSubjectUpdateForm($form)
    {
        $values = $form->getValues();
         
        $subject = new Subject($this->database);
         
        
        // Unikatni zkratka
        if ($subject->where('id != ?', $values['id'])->where('shortcut = ?', $values['shortcut'])->count()) {
            
            $form->addError('Změněná zkratka v systému již existuje.');
        }
         
        // Prirazeny rocnik
        $assignedGrade = false;
        foreach ($values['grades'] as $g) {
            if ($g) $assignedGrade = true;
        }
        if (!$assignedGrade) {
            $form->addError('Předmět musí mít přiřazený alespoň jeden ročník.');
        }
    }
    
    public function subjectFormSucceeded($form)
    {
        // získani dat z formulare
        $values = $form->getValues();
        $this->setMyAutorizator();
        
        if (!$this->user->isAllowed('subject', 'insert')) {
            throw new AuthenticationException('Nemáte oprávnění přidávat předměty.');
            return;
        }
                
        $subject = new Subject($this->database);
        $subject_id = $subject->insert(array(
            'shortcut' => $values['shortcut'],
            'name' => $values['name'],
            'description' => $values['description']
        ));
        
        $subject2grade = new Subject2Grade($this->database);
        foreach ($values['grades'] as $g) {
            $subject2grade->insert(array(
                'subject_id' => $subject_id,
                'grade_id' => $g
            ));
        }
        
        $this->flashMessage('Předmět přidán.', 'success');
    
    }

    public function subjectUpdateFormSucceeded($form)
    {
        // získani dat z formulare
        $values = $form->getValues();
        $this->setMyAutorizator();
    
        if (!$this->user->isAllowed('subject', 'update')) {
            throw new AuthenticationException('Nemáte oprávnění upravovat předměty.');
            return;
        }
    
        $subject = new Subject($this->database);
        $subject->where('id', $values['id'])->update(array(      
            'shortcut' => $values['shortcut'],
            'name' => $values['name'],
            'description' => $values['description']
        ));
    
        $subject2grade = new Subject2Grade($this->database);
        $subject2grade->updateRelations($values['id'], $values['grades']);        
    
        $this->flashMessage('Předmět '.$values['name'].' upraven.', 'success');
        $this->redirect('Subject:show',$values['id']);
    
    }    
    
    public function actionDelete($subjectId) {
        $this->setMyAutorizator();
        if (!$this->user->isAllowed('subject', 'delete')) {
            throw new AuthenticationException('Nemáte oprávnění mazat předměty.');
            return;
        }
        
        $subject = new Subject($this->database);
        $subject->where('id', $subjectId)->delete();
        
        $this->flashMessage('Předmět byl smazán.', 'success');
        $this->redirect('Homepage:');        
    }

    public function renderUpdate($subjectId) {
        $this->setMyAutorizator();
        if (!$this->user->isAllowed('subject', 'update')) {
            throw new AuthenticationException('Nemáte oprávnění upravovat předměty.');
            return;
        }
    
        $subject = new Subject($this->database);
        $subject = $subject->get($subjectId);
        $this->template->subject = $subject;
        
        $subject2grade = new Subject2Grade($this->database);
        $subject2grade = $subject2grade->select('grade_id')->where('subject_id', $subjectId);
        
        $grade = new Grade($this->database);
        $grade = $grade->select('id');
        
        $grades = array();
        
        foreach($subject2grade as $g) {
            $grades[]=$g->grade_id;
        }
        
        $values = array(
            'id' => $subjectId,
            'shortcut' => $subject->shortcut,
            'name' => $subject->name,
            'description' => $subject->description,
            'grades' => $grades  
        );
        
         $this['subjectUpdateForm']->setDefaults($values);

    }
    
    
}
