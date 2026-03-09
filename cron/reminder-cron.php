<?php
require_once __DIR__ . '/../config/config.php';
// Composer autoload for PHPMailer
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) die("Installare dipendenze composer.
");
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$lessons=$pdo->query("SELECT l.*, c.title course_title, u.id teacher_id, u.email teacher_email FROM lessons l JOIN courses c ON c.id=l.course_id LEFT JOIN users u ON u.id=c.teacher_id WHERE l.status='scheduled' AND l.lesson_date BETWEEN CURDATE()+INTERVAL 1 DAY AND CURDATE()+INTERVAL 2 DAY")->fetchAll();
foreach($lessons as $lesson){
    $days=(new DateTime())->diff(new DateTime($lesson['lesson_date']))->days;
    $flag=$days===2?'reminder_2d_sent':'reminder_1d_sent';
    if((int)$lesson[$flag]===1) continue;
    $recipients=[['id'=>$lesson['teacher_id'],'email'=>$lesson['teacher_email']]];
    $st=$pdo->prepare('SELECT u.id,u.email FROM enrollments e JOIN users u ON u.id=e.student_id WHERE e.course_id=? AND e.status="active"');$st->execute([$lesson['course_id']]);
    $recipients=array_merge($recipients,$st->fetchAll());
    foreach($recipients as $r){
        if(empty($r['email'])) continue;
        $mail=new PHPMailer(true);
        try{
            $mail->isMail(); $mail->setFrom('noreply@formaflow.test','FormaFlow'); $mail->addAddress($r['email']);
            $mail->Subject='Reminder lezione '.$lesson['title'];
            $mail->Body='Promemoria lezione '.$lesson['title'].' del '.$lesson['lesson_date'].' Link Meet: '.$lesson['google_meet_link'];
            $mail->send();
            $log=$pdo->prepare('INSERT INTO email_logs(lesson_id,user_id,email_to,subject,body,status,sent_at) VALUES(?,?,?,?,?,"sent",NOW())');
            $log->execute([$lesson['id'],$r['id'],$r['email'],$mail->Subject,$mail->Body]);
        }catch(Throwable $e){
            $log=$pdo->prepare('INSERT INTO email_logs(lesson_id,user_id,email_to,subject,body,status,error_message,sent_at) VALUES(?,?,?,?,?,"failed",?,NOW())');
            $log->execute([$lesson['id'],$r['id'],$r['email'],'Reminder lezione','Errore invio',$e->getMessage()]);
        }
    }
    $pdo->prepare("UPDATE lessons SET {$flag}=1 WHERE id=?")->execute([$lesson['id']]);
}
echo "Reminder completati
";
