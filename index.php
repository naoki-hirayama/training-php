<?php
session_start();
require_once('function/db_connect.php');
require_once('function/Pager.php');
require_once('function/function.php');
require_once('models/UserRepository.php');
require_once('models/PostRepository.php');
require_once('models/ReplyRepository.php');

$database = db_connect();
$user_repository = new UserRepository($database);
$post_repository = new PostRepository($database);
$reply_repository = new ReplyRepository($database);

$picture_max_size = $user_repository::MAX_PICTURE_SIZE;
$select_color_options = PostRepository::getSelectColorOptions();

if (isset($_SESSION['user_id'])) {
    $user_info = $user_repository->fetchById($_SESSION['user_id']);
}
// POSTでアクセスされたら投稿処理を行う
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = $_POST;
    
    if (isset($_FILES['picture'])) {
        $values['picture'] = $_FILES['picture'];
    }
    $errors = $post_repository->validate($values);
    // 成功した場合はDBへ保存してsend.phpにリダイレクトする
    if (empty($errors)) {
        if (isset($_SESSION['user_id'])) {
            $post_repository->create($values, $_SESSION['user_id']);
        } else {
            $post_repository->create($values);
        }
        header('Location: send.php');
        exit;
    }
} else {
    // GETでアクセスされた時
    $total_records = $post_repository->fetchCount();
    $max_pager_range = 10;
    $per_page_records = 5;
    
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    
    $pager = new Pager($total_records, $max_pager_range, $per_page_records);
    $pager->setCurrentPage($page);
    $offset = $pager->getOffset();
    $per_page_records = $pager->getPerPageRecords();
    $posts = $post_repository->fetchByOffSetAndLimit($offset, $per_page_records);
    
    $user_ids = [];
    $post_ids = [];
    foreach ($posts as $post) {
        $post_ids[] = $post['id'];
        if (isset($post['user_id'])) {
            $user_ids[] = $post['user_id'];
        }
    }
    
    if (!empty($user_ids)) {
        $users = $user_repository->fetchByIds($user_ids);
        $user_names = array_column($users, 'name', 'id');
    }
    
    $reply_counts = $reply_repository->fetchCountByPostIds($post_ids);
}

include('views/index.php');
