<?php

namespace App\Model;

use Nette\Http\FileUpload;
/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých přiložených souborechke článkům.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - varchar(127) name
 *  - varchar(255) extension
 *  - varchar(255) mimeType
 *  - int topic_id FK
 *  - int user_id FK
 *  - DateTime created_at
 *  - text description
 *  
 */
class Attachement extends \Nette\Database\Table\Selection {
    private $table = "Attachement";
    private $db;
    /**
     * Path to attachements
     * @var string
     */
    const SAVE_DIR = "../data/attachements/";
    const ENABLED_EXTENSION = "doc docx xls xlsx jpg jpeg png zip ppt pptx pdf";
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    /**
     * Get all attachements to Topic
     * @param int $topicId
     * @return Attachement[]
     */
    public function getByTopic($topicId) {
        return $this->where('topic_id', $topicId);
    }

    /**
     * Get path to Attachemement
     * @param int $attachementId
     * @return string
     */
    public function getPathById($attachementId) {
        $ext = $this->get($attachementId)->extension;
        if (!$ext) {
            return null;
        }
        return self::SAVE_DIR . $attachementId . '.' . $ext;
    }
    
    /**
     * Get file extension example: pdf
     * @param int $attachementId
     * @return string
     */
    public function getExtensionById($attachementId) {
        $filename = (string) $this->get($attachementId)->file;
        return self::getExtensionByName($filename);
    }
    
    public static function getExtensionByName($filename) {
        $tmp = explode('.', $filename);
        return end($tmp);
    }
    
    public function safeDelete($attachement_id) {
        $attachement = new Attachement($this->db);
        $a = $attachement->where('id', $attachement_id)->fetch();
        $filename = self::SAVE_DIR . $attachement_id . '.' . $a['extension'];
        if (file_exists($filename)) {
            unlink($filename);
        }
        $this->where('id', $attachement_id)->delete();
    }
    
    /**
     * Save file and insert information into database
     * @param FileUpload $file
     * @param int $topic_id
     * 
     */
    public function insertFile(FileUpload $file, $topic_id, $user_id, $description = null) {
        $attachement = new Attachement($this->db);
        $filename = $file->getName();
        $ext = self::getExtensionByName($filename);

        // povolene pripony
        $enabledExt = explode(' ', self::ENABLED_EXTENSION);
        if (!in_array($ext, $enabledExt)) return false;
        
        
        // reseni mime typu
        switch ($ext) {
            case "pdf": $mime = "application/pdf"; break;
            case "jpg": $mime = "image/jpeg"; break;
            case "jpeg": $mime = "image/jpeg"; break;
            case "png": $mime = "image/png"; break;
            case "zip": $mime = "application/zip"; break;
            case "rar": $mime = "application/x-rar-compressed"; break;
            case "doc": $mime = "application/msword"; break;
            case "docx": $mime = "application/pdf"; break;
            case "ppt": $mime =  "application/vnd.ms-powerpoint"; break;
            case "xls": $mime =  "application/vnd.ms-excel"; break;
            case "xlsx": $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
            case "xltx": $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.template"; break;
            case "potx": $mime = "application/vnd.openxmlformats-officedocument.presentationml.template"; break;
            case "ppsx": $mime = "application/vnd.openxmlformats-officedocument.presentationml.slideshow"; break;
            case "pptx": $mime = "application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
            case "sldx": $mime = "application/vnd.openxmlformats-officedocument.presentationml.slide"; break;
            case "docx": $mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
            case "dotx": $mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.template"; break;
            case "xlam": $mime = "application/vnd.ms-excel.addin.macroEnabled.12"; break;
            case "xlsb": $mime = "application/vnd.ms-excel.sheet.binary.macroEnabled.12"; break;
            default: $mime = "application/octet-stream"; // a binary file
        }
                
        
        $attachement->insert(array(
            'name' => $filename,
            'mimeType' => $mime,
            'extension' => $ext,
            'topic_id' => $topic_id,
            'user_id' => $user_id,
            'description' => $description
        ));
        
        $lastAttachement = $attachement->select('id')->order('id DESC')->limit(1)->fetch();
        
        if (!move_uploaded_file($file->getTemporaryFile(), self::SAVE_DIR . $lastAttachement['id'] . '.' . $ext)) {
            return false;   
        }
        return true;
    }
    
}
