    <?php
    include("./db_write.php");

    $pdo = db_access();
    $value1 = '';
    if (isset($_POST['value1'])) {
        $value1 = $_POST['value1'];
        $value1 = htmlspecialchars($value1);
        $value1 = stripslashes($value1);
    }

    date_default_timezone_set('Asia/Tokyo');
    $unitime = date('Y-m-d H:i:s');
    $index = 0;

    if (isset($_POST['value2'])) {
        $currencys = explode('@', $_POST['value2'], 6);

        foreach ($currencys as $row) {
            $index++;

            $rows = explode('=', $row, 2);
            $currency = htmlspecialchars($rows[0]);
            $currency = stripslashes($currency);
            $rate = htmlspecialchars($rows[1]);
            $rate = stripslashes($rate);

            $query1 = 'UPDATE exchange_rate SET currency = "' . $currency . '", rate = "' . $rate . '", updatetime = "' . $unitime . '" WHERE exchange_rate.id = ' . $index;

            $result = db_prepare_sql($query1, $pdo);
        }
    }
    // DBからデータ取得
    $query2 = 'SELECT * FROM exchange_rate';
    $array = db_prepare_sql($query2, $pdo);
    $php_array = array();

    foreach ($array as $row) {
        $currency = trim($row['currency']);
        $rate = trim($row['rate']);
        $updatetime = $row['updatetime'];

        $php_array = $php_array + array('updatetime' => $updatetime);
        $php_array = $php_array + array($currency => $rate);
    }
    $json_array = json_encode($php_array, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    db_close($pdo);
    echo $json_array;
    ?>
