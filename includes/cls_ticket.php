<?php

/**
 * PHP code
 * ============================================================================
 * filename: cls_ticket.php
 * ----------------------------------------------------------------------------
 * page: no data
 * ============================================================================
 * modified: 11:35 07.06.2017
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class cls_ticket
{

    var $db  = NULL;
    var $prioritet = array('','Низкий','Средний','Высокий');
    var $department=array('','Технический','Бухгалтерия','Склад');

    function __construct(&$db, $ticket_table)
    {
        $this->cls_ticket($db, $ticket_table);
    }

    function cls_ticket(&$db, $ticket_table)
    {
        $this->ticket_table = $ticket_table;
        $this->db  = &$db;
    }
    function insert($title, $message,$department, $prioritet,$images){
        $date = date("Y-m-d H:i:s");
        $sql = 'INSERT INTO '. $this->ticket_table .'(date,title, message, department,prioritet,images) VALUES ("'.$date.'","'.$title.'","'.$message.'","'.$department.'","'.$prioritet.'","'.$images.'")';
        return $this->db->query($sql);
    }
    function insert_product($title, $message,$prioritet,$images,$email){
        $date = date("Y-m-d H:i:s");
        $sql = 'INSERT INTO '. $this->ticket_table .'(date,title, message,prioritet,images,email) VALUES ("'.$date.'","'.$title.'","'.$message.'","'.$prioritet.'","'.$images.'","'.$email.'")';
        return $this->db->query($sql);
    }
    function get_itemlist($status,$department){
        if($department>0){
            $filter = ' AND department ='.$department;
        }
        else{
            $filter='';
        }
        $sql = 'SELECT * FROM '. $this->ticket_table . ' WHERE status='.$status.$filter.' ORDER BY id DESC';
        $itemlist = $this->db->getAll($sql);
        return $itemlist;
    }
    function get_productlist($email){
        $sql = 'SELECT * FROM '. $this->ticket_table. ' WHERE email= "'.$email.'"'; //.' ORDER BY id DESC';
        return $this->db->getAll($sql);
    }
	function get_prioritet(){
        return $this->prioritet;
    }
    function get_department(){
        return $this->department;
    }
    function checkTitle($str){
        $str = htmlspecialchars($str);
        return $str; 
    }
    function checkMessage($str){
        $str = htmlspecialchars($str);
        return $str; 
    }
    function checkEmail($str){
        $str = htmlspecialchars($str);
        return $str; 
    }
    function get_ticket($id){
        $sql = 'SELECT * FROM '. $this->ticket_table . ' WHERE id='.$id;
        return $this->db->getRow($sql);
    }
    function finish($id){
        $sql = 'UPDATE '.$this->ticket_table.' SET status = 1 WHERE id='.$id;
        return $this->db->query($sql);
    }
    function insert_message($ticket_id,$message){
         $date = date("Y-m-d H:i:s");
        $sql = 'INSERT INTO ticket_message (date,ticket_id,message) VALUES ("'.$date.'","'.$ticket_id.'","'.$message.'")';
        return $this->db->query($sql);
    }

    function get_messages($ticket_id){
         $sql = 'SELECT * FROM ticket_message WHERE ticket_id='.$ticket_id.' ORDER BY id DESC';
        return $this->db->getAll($sql);
    }
}
?>
