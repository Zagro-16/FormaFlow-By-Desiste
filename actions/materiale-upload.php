<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin','docente']);
if (!is_post() || empty($_FILES['materiale'])) redirect('dashboard.php');
$f=$_FILES['materiale'];
if ($f['error']!==UPLOAD_ERR_OK) { set_flash('danger','Upload fallito'); redirect($_SERVER['HTTP_REFERER']??'dashboard.php'); }
$ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
$allowed=['pdf','doc','docx','ppt','pptx','zip'];
if(!in_array($ext,$allowed,true)){ set_flash('danger','Formato non consentito'); redirect($_SERVER['HTTP_REFERER']??'dashboard.php');}
$target='uploads/materiali/'.uniqid('mat_',true).'.'.$ext;
move_uploaded_file($f['tmp_name'], __DIR__.'/../'.$target);
$stmt=$pdo->prepare('INSERT INTO materials (course_id,uploader_id,title,file_path) VALUES (?,?,?,?)');
$stmt->execute([$_POST['course_id'],$_SESSION['user']['id'],$_POST['title']?:$f['name'],$target]);
set_flash('success','Materiale caricato');redirect($_SERVER['HTTP_REFERER']??'dashboard.php');