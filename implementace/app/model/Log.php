<?php

namespace App\Model;

/**
 * Třída obstarávající přístup do databázovou tabulku s údaji o jednotlivých interakcích se systémem.
 * Převzaté datové položky z databáze:
 *  - int id
 *  - int user_id FK
 *  - int topic_id FK
 *  - int action_id FK
 *  - DateTime created_at
 *  - text note
 */
class Log extends \Nette\Database\Table\Selection {
    private $table = "Log";
    private $db;
    
    const ACTION_VISIT_ID = '1'; // id of Visit action
    const ACTION_LOGIN_ID = '2'; // id of Login action
    
    public function __construct(\Nette\Database\Context $database) {
        parent::__construct($database->getConnection(), $this->table, 
                $database->getDatabaseReflection());
        $this->db = $database;
    }
    
    
    // --------------------------------------------
    // ADD
    
    public function addInteraction($user_id, $topic_id, $action_id, $note = '') {
        $log = new Log($this->db);
        $log->insert(array(
            'user_id' => $user_id,
            'topic_id' => $topic_id,
            'action_id' => $action_id,
            'note' => $note
        ));
    }
    
    public function addVisit($user_id, $topic_id, $note = '') {
        $this->addInteraction($user_id, $topic_id, self::ACTION_VISIT_ID, $note);
    }
    
    public function addLogin($user_id, $note = '') {
        $this->addInteraction($user_id, null, self::ACTION_LOGIN_ID, $note);
    }

    // --------------------------------------------
    // GET COUNT
    
    public function getVisitesByTopic($topic_id, $timestamp_since = 0, $timestamp_to = null) {
        $log = new Log($this->db);
        self::prepareTime($timestamp_since, $timestamp_to);
        return $log
            ->where('created_at >= ?', $timestamp_since)
            ->where('created_at <= ?', $timestamp_to)
            ->where('topic_id', $topic_id)
            ->where('action_id', self::ACTION_VISIT_ID)
            ->count('id');       
    }

    public function getVisitesByUser($user_id, $timestamp_since = 0, $timestamp_to = null) {
        $log = new Log($this->db);
        self::prepareTime($timestamp_since, $timestamp_to);
        return $log
            ->where('created_at >= ?', $timestamp_since)
            ->where('created_at <= ?', $timestamp_to)
            ->where('user_id', $user_id)
            ->where('action_id', self::ACTION_VISIT_ID)
            ->count('id');
    }
    
    public function getVisitesByUserAndTopic($user_id, $topic_id, $timestamp_since = 0, $timestamp_to = null) {
        $log = new Log($this->db);
        self::prepareTime($timestamp_since, $timestamp_to);
        return $log
            ->where('created_at >= ?', $timestamp_since)
            ->where('created_at <= ?', $timestamp_to)
            ->where('user_id', $user_id)
            ->where('topic_id', $topic_id)
            ->where('action_id', self::ACTION_VISIT_ID)
            ->count('id');        
    }
    
    public function getLoginsByUser($user_id, $timestamp_since = 0, $timestamp_to = null) {
        $log = new Log($this->db);
        self::prepareTime($timestamp_since, $timestamp_to);
        return $log
            ->where('created_at >= ?', $timestamp_since)
            ->where('created_at <= ?', $timestamp_to)
            ->where('user_id', $user_id)
            ->where('action_id', self::ACTION_LOGIN_ID)
            ->count('id');      
    }
    
    public static function prepareTime(&$since, &$to) {
        if (!$to) $to = time();
        if (!$since) $since = 0;
        if ($since > $to) {
            $tmp = $since;
            $to = $since;
            $since = $tmp;
        }   
    }
    
    //----------------------------------------------------
    // GET INTERACTION 
    public function getFirstVisitOfTopic($topic_id) {
        $log = new Log($this->db);
        return $log
            ->where('topic_id', $topic_id)
            ->where('action_id', self::ACTION_VISIT_ID)
            ->order('created_at ASC')
            ->limit(1)
            ->fetch();
    }

    public function getListVisitsOfTopic($topic_id, $limit=1000, $chronolog = true) {
        $log = new Log($this->db);
        $order = $chronolog ? 'ASC' : 'DESC';
        return $log
        ->where('topic_id', $topic_id)
        ->where('action_id', self::ACTION_VISIT_ID)
        ->order('created_at '.$order)
        ->limit($limit);
    }

    public function getListVisitsOfUser($user_id, $limit=1000, $chronolog = true, $distinct = false) {
        $log = new Log($this->db);
        $order = $chronolog ? 'ASC' : 'DESC';
        $dst = $distinct ? 'DISTINCT ' : '';
        return $log
            ->select($dst . 'topic_id')
            ->where('user_id', $user_id)
            ->where('topic_id != ?', 'null')
            ->where('action_id', self::ACTION_VISIT_ID)
            ->order('created_at '.$order)
            ->limit($limit);
    }
    
    public function getLastVisitOfTopic($topic_id) {
        $log = new Log($this->db);
        return $log
        ->where('topic_id', $topic_id)
        ->where('action_id', self::ACTION_VISIT_ID)
        ->order('created_at DESC')
        ->limit(1)
        ->fetch();
    }

    public function getFirstLoginOfUser($user_id) {
        $log = new Log($this->db);
        return $log
        ->where('user_id', $user_id)
        ->where('action_id', self::ACTION_LOGIN_ID)
        ->order('created_at ASC')
        ->limit(1)
        ->fetch();
    }

    public function getListLoginsOfUser($user_id, $limit=1000, $chronolog = true) {
        $log = new Log($this->db);
        $order = $chronolog ? 'ASC' : 'DESC';
        return $log
        ->where('user_id', $user_id)
        ->where('action_id', self::ACTION_LOGIN_ID)
        ->order('created_at '.$order)
        ->limit($limit);
    }
    
    
    public function getLastLoginOfUser($user_id) {
        $log = new Log($this->db);
        return $log
        ->where('user_id', $user_id)
        ->where('action_id', self::ACTION_LOGIN_ID)
        ->order('created_at DESC')
        ->limit(1)
        ->fetch();
    }
    
}
