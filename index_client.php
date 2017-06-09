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
	$prioritet = $_POST['prioritet'];
	$title = $ticket->checkTitle($_POST['title']);
	$message = $ticket->checkMessage($_POST['message']);
	$email = $ticket->checkEmail($_POST['email']);
	$result = $ticket->insert_product($title,$message,$prioritet,$_images,$email);
	if($result>0){
		echo "<p>Товар успешно добавлен</p>";
	}
}
else if($action=='show'){
	$id = $_GET['id'];
	$item = $ticket->get_ticket($id);
	$prioritet = $ticket->get_prioritet();
	if(strlen($item['images'])>0){
		$_images =explode(',', $item['images']);
		$smarty->assign('images',$_images);
	}
	$messages = $ticket->get_messages($id);
	$smarty->assign('messages',$messages);
	$smarty->assign('prioritet', $prioritet);
	$smarty->assign('ticket',$item);
	$smarty->display('product_page.htm');
}
else if($action=='post'){
	$ticket_id = $_POST['ticket_id'];
	$message = $_POST['message'];
	$result = $ticket->insert_message($ticket_id,$message);
	if($result>0){
		echo "<p>Сообщение успешно добавлено</p>";
		echo '<p><a href="index_client.php?act=show&id='.$ticket_id.'">Перейти назад</a></p';
	}
}
else if($action=='list'){
	$email = $_GET['email'];
	$itemlist = $ticket->get_productlist($email);
	if(count($itemlist)>0){
		$prioritet_list = $ticket->get_prioritet();
		$smarty->assign('product_list', $itemlist);
		$smarty->assign('prioritet_list', $prioritet_list);
		$smarty->display('product_list.htm');
	}
	else{
		echo "Вы не создавали запрос на товар";
		echo '<p><a href="index_client.php">Перейти назад</a></p';
	}
	
}
else{
	$prioritet_list = $ticket->get_prioritet();
	$smarty->assign('prioritet_list', $prioritet_list);
	$smarty->display('product_form.htm');
}
?>