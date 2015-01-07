<?php

namespace App\Presenters;

use Nette, App\Model;
use App\Model\Attachement;


/**
 * Attachement presenter.
 */
class AttachementPresenter extends BasePresenter
{

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    /**
     * Download an attachement by id
     * @param int $attachementId
     */
    public function renderDownload($attachementId) {
        if (!$this->user->isAllowed('attachement', 'read')) {
            $this->redirect('Homepage:');
            $this->flashMessage('Nemáte oprávnění číst přílohu článku.','warning');
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
    
        header('Content-type: $attachement->get($attachementId)->mimeType');
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