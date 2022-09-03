<!DOCTYPE html>

<head></head>

<body>
    check exchange_rate<br />
<?php
//$link = mysqli_connect('localhost', 'id19476344_loser', ')l>?Gnj^Sv\lNh<6', 'id19476344_database');
$link = mysqli_connect('localhost', 'kinokonosato', 'P00027511wy3', 'test');

$echo_str = "";//レスポンス用の文字列格納用
// 接続状況をチェックします
if (mysqli_connect_errno()) {
    die("cannot access db:" . mysqli_connect_error() . "\n");
} else {
    $echo_str = "DB OK.\n";
}

$value1 = '';
if(isset($_POST['value1']))
{
$value1 = $_POST['value1'];
$value1 = htmlspecialchars($value1);
$value1 = stripslashes($value1);

$echo_str = $echo_str . "$value1" . "\n";
}
else{
    return;
}

if(isset($_POST['value2']))
{
$value2 = '';
$value2 = $_POST['value2'];
$value2 = htmlspecialchars($value2);
$value2 = stripslashes($value2);

$echo_str = $echo_str ."$value2" . "\n";
}
else{
    return;
}

$query1 = 'UPDATE exchange_rate SET rate = "' .$value2. '", updatetime = NOW() WHERE exchange_rate.id = 1';
if (mysqli_query($link, $query1)) {
    $echo_str = $echo_str . "UPDATE success";
}

echo "$echo_str";

mysqli_close($link);

?>
</body>

</html>