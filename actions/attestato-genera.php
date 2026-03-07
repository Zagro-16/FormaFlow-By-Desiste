<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);
// Richiede dompdf via composer.
if (!class_exists('Dompdf\Dompdf')) { set_flash('danger','Installa dompdf con composer require dompdf/dompdf'); redirect($_SERVER['HTTP_REFERER']??'admin/attestati.php'); }
use Dompdf\Dompdf;
$studentId=(int)($_POST['student_id']??0); $courseId=(int)($_POST['course_id']??0);
$q=$pdo->prepare('SELECT u.full_name,c.title,c.total_hours FROM users u JOIN enrollments e ON e.student_id=u.id JOIN courses c ON c.id=e.course_id WHERE u.id=? AND c.id=? LIMIT 1');
$q->execute([$studentId,$courseId]); $row=$q->fetch(); if(!$row){ set_flash('danger','Dati non trovati'); redirect('admin/attestati.php'); }
$code='AT-'.date('Ymd').'-'.strtoupper(substr(md5((string)microtime()),0,8));
$html='<h1>Attestato di frequenza</h1><p>Si certifica che <strong>'.e($row['full_name']).'</strong> ha completato il corso <strong>'.e($row['title']).'</strong> per '.e((string)$row['total_hours']).' ore.</p><p>Codice: '.$code.'</p>';
$dompdf=new Dompdf(); $dompdf->loadHtml($html); $dompdf->setPaper('A4','landscape'); $dompdf->render();
$file='uploads/attestati/'.$code.'.pdf'; file_put_contents(__DIR__.'/../'.$file,$dompdf->output());
$i=$pdo->prepare('INSERT INTO certificates(course_id,student_id,certificate_code,pdf_path,issued_at) VALUES(?,?,?,?,NOW())'); $i->execute([$courseId,$studentId,$code,$file]);
set_flash('success','Attestato generato'); redirect('admin/attestati.php');