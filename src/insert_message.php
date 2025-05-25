<?php
    function mbTrim($pString){
        return preg_replace('/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u', '', $pString);
    }

    // 入力値の確認（投稿者）
    $is_valid_author_name = true;
    $input_author_name = '';

    if(isset($_POST['author_name'])) {
        $input_author_name = mbTrim(str_replace("\r\n", "\n", $_POST['author_name']));
        $_SESSION['input_pre_author_name'] = $_POST['author_name'];
    } else {
        $is_valid_author_name = false;
    }

    if($is_valid_author_name && mb_strlen($input_author_name) > 30) {
        $input_valid_author_name = false;
        $_SESSION['input_error_author_name'] = 'ニックネームは 30 文字以内で入力してください。 (現在 ' . mb_strlen($input_author_name) . ' 文字）';
    }

    // 入力値の確認（投稿内容）
    $is_valid_message = true;
    $input_message = '';

    if(isset($_POST['message'])) {
        $input_message = mbTrim(str_replace("\r\n", "\n", $_POST['message']));
        $_SESSION['input_pre_message'] = $_POST['message'];
    } else {
        $is_valid_message = false;
    }

    if($is_valid_message && $input_message === '') {
        $is_valid_message = false;
        $_SESSION['input_error_message'] = '投稿内容の入力は必須です。';
    }

    if($is_valid_message && mb_strlen($input_message) > 1000) {
        $is_valid_message = false;
        $_SESSION['input_error_message'] = '投稿内容は 1000 文字以下で入力してください。 (現在 ' . mb_strlen($input_message) . ' 文字）';
    }

    // 投稿をデータベースへ保存する処理
    if($is_valid_author_name && $is_valid_message) {
        if($input_author_name === '') {
            $input_author_name = '匿名さん';
        }

        $query = 'INSERT INTO posts (author_name, message) VALUES (:author_name, :message)';

        $stmt = $dbh->prepare($query);

        $stmt->bindValue(':author_name', $input_author_name, PDO::PARAM_STR);
        $stmt->bindValue(':message', $input_message, PDO::PARAM_STR);

        $stmt->execute();
        $_SESSION['action_success_text'] = '投稿しました';
        $_SESSION['action_error_text'] = '';
        $_SESSION['input_error_author_name'] = '';
        $_SESSION['input_error_message'] = '';
        $_SESSION['input_pre_author_name'] = '';
        $_SESSION['input_pre_message'] = '';
    } else {
        $_SESSION['action_success_text'] = '';
        $_SESSION['action_error_text'] = '入力内容を確認してください';
    }

    header('Location: /');
    exit();
?>