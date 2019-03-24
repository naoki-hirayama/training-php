<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
} else if ($_SESSION['user_id'] !== $_GET['id']) {
    header("Location: index.php"); 
    exit;
}

//パラメーターがない時どうする？
require_once('function/db_connect.php');
require_once('function/function.php');
$database = db_connect();

$sql = 'SELECT * FROM users WHERE id = :id';

$statement = $database->prepare($sql);

$statement->bindParam(':id', $_GET['id']);

$statement->execute();

$user = $statement->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    if (!password_verify($_POST['current_password'],$user['password'])) {
        $errors[] = "パスワードが間違っています。";
    }
    
    
    
    $new_password = trim(mb_convert_kana($_POST['new_password'], 's'));
    if (mb_strlen($new_password, 'UTF-8') === 0) {
        $errors[] = "パスワードは入力必須です。";
    } else if (!preg_match("/^[a-zA-Z0-9]+$/", $new_password)) {
        $errors[] = "パスワードは半角英数字です。";
    } else if (mb_strlen($new_password, 'UTF-8') < 4) {
        $errors[] = "パスワードは４文字以上です。";
    } else if (mb_strlen($new_password, 'UTF-8') > 30) {
        $errors[] = "パスワードが長すぎます。";
    }
    
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
       $errors[] = "確認用パスワードが一致しません。";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    }
    
    if (empty($errors)) {
        
        $sql = 'UPDATE users SET password = :password WHERE id = :id';
            
        $statement = $database->prepare($sql);
        
        $statement->bindParam(':id', $_GET['id']);
        $statement->bindParam(':password', $password_hash);
        
        $statement->execute();
       
        $statement = null;
        
        header('Location: edit.php?id='.$_GET['id'].'');
        exit;
    }
}
include('views/password.php');