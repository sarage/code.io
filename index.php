<?php
header('Content-Type: text/html; charset=utf-8');

define('IN_ECS', true);

// init
require(dirname(__FILE__) . '/includes/init.php');

require(ROOT_PATH . 'includes/cls_ticket.php');
$ticket = new cls_ticket($db, 'tickets');

$action = $_GET['act'];
if($action=='create'){
	$target_dir = ROOT_PATH ."uploads/";
	$images= array();
	foreach ($_FILES['images']['tmp_name'] as $key => $value){
        if ($_FILES['images']['size'][$key] > 0)
        {
            $ext = explode('.', $_FILES['images']['name'][$key]);
            $filename=rand(9999,999999999).'.'.$ext[1];
            $target_file = $target_dir.$filename;
            if (move_uploaded_file($_FILES["images"]["tmp_name"][$key], $target_file)){
            	$images[]=$filename;
            }
            else{  
              echo "Простите, произошла ошибка при загрузке файла.";
          }
        }
    }
    $_images = implode(',', $images);
	$department = $_POST['department'];
	$prioritet = $_POST['prioritet'];
	$title = $ticket->checkTitle($_POST['title']);
	$message = $ticket->checkMessage($_POST['message']);
	$result = $ticket->insert($title,$message,$department,$prioritet,$_images);
	if($result>0){
		echo "<p>Задача успешно создалась</p>";
		echo '<p><a href="index.php">К списку тикетов</a></p';
	}
}
else if($action=='add'){
	$department_list = $ticket->get_department();
	$prioritet_list = $ticket->get_prioritet();
	$smarty->assign('department_list', $department_list);
	$smarty->assign('prioritet_list', $prioritet_list);
	$smarty->display('ticket_form.htm');
}
else if($action=='show'){
	$id = $_GET['id'];
	$item = $ticket->get_ticket($id);
	$department = $ticket->get_department();
	$prioritet = $ticket->get_prioritet();
	if(strlen($item['images'])>0){
		$_images =explode(',', $item['images']);
		$smarty->assign('images',$_images);
	}
	$messages = $ticket->get_messages($id);
	$smarty->assign('messages',$messages);
	$smarty->assign('department', $department);
	$smarty->assign('prioritet', $prioritet);
	$smarty->assign('ticket',$item);
	$smarty->display('ticket_page.htm');
}
elseif ($action=='done') {
	$id = $_GET['id'];
	$result=$ticket->finish($id);
	if($result>0){
		echo "<p>Задача успешно завершена</p>";
		echo '<p><a href="index.php">К списку тикетов</a></p';
	}
}
else if($action=='post'){
	$ticket_id = $_POST['ticket_id'];
	$message = $_POST['message'];
	$result = $ticket->insert_message($ticket_id,$message);
	if($result>0){
		echo "<p>Сообщение успешно добавлено</p>";
		echo '<p><a href="index.php?act=show&id='.$ticket_id.'">Перейти назад</a></p';
	}
}
else{	
	$itemlist = $ticket->get_itemlist(0,'');
	$department_list = $ticket->get_department();
	$prioritet_list = $ticket->get_prioritet();

	$smarty->assign('ticket_list', $itemlist);
	$smarty->assign('department_list', $department_list);
	$smarty->assign('prioritet_list', $prioritet_list);
	$smarty->display('ticket_list.htm');
}
?>