<?php
if (isset($_GET['location'])) {
    $_PDO = db_access();
    $_SQL = "INSERT INTO `userlocation` (`id`, `user`, `location`,`ipadder`,`host`,`agent`,`language`, `updateTime`) VALUES (NULL, '" . $_GET['user'] . "' , '" . $_GET['location'] . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $_SERVER['REMOTE_HOST'] . "','" . $_SERVER['HTTP_USER_AGENT'] . "','" . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . "', NOW())";
    db_prepare_sql($_SQL, $_PDO);

    echo json_encode("ok");
    db_close($_PDO);
} else {
    echo json_encode("false");
}

function db_access()
{
    // DB接続情報
    $user = 'id19476344_loser';
    $pass = 'kG((Ju1}&US8FC2f';
    $dbnm = 'id19476344_database';
    $host = 'localhost';
    // 接続先DBリンク
    $connect = "mysql:host={$host};dbname={$dbnm}";

    try {
        // DB接続
        $pdo = new PDO($connect, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "<p>DB接続エラー</p>";
        echo $e->getMessage();
        exit();
    }

    return $pdo;
}

function db_close($pdo)
{
    unset($pdo);
}

function db_prepare_sql(string $sql, $pdo)
{
    try {
        // SQL実行
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // 結果の取得
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);
    } catch (Exception $e) {
        echo "<p>DB接続エラー</p>";
        echo $e->getMessage();
        exit();
    }

    return $result;
}
