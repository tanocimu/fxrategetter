<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>real time loser checker</title>
    <style>
        * {
            text-align: center;
        }

        .usdjpy_value {
            font-size: 2em;
            font-weight: bold;
            color: blue;
        }

        .loser_value {
            font-size: 5em;
            font-weight: bold;
            color: red;
        }
    </style>
    <?php
    include("./db_write.php");

    $pdo = db_access();

    $value1 = '';
    if (isset($_POST['value1'])) {
        $value1 = $_POST['value1'];
        $value1 = htmlspecialchars($value1);
        $value1 = stripslashes($value1);

        $echo_str = $echo_str . "$value1" . "\n";
    }

    if (isset($_POST['value2'])) {
        $value2 = '';
        $value2 = $_POST['value2'];
        $value2 = htmlspecialchars($value2);
        $value2 = stripslashes($value2);

        $echo_str = $echo_str . "$value2" . "\n";
        $query1 = 'UPDATE exchange_rate SET rate = "' . $value2 . '", updatetime = NOW() WHERE exchange_rate.id = 1';
        $result = db_prepare_sql($query1, $pdo);
    }



    $query2 = 'SELECT * FROM exchange_rate WHERE id = 1';
    $array = db_prepare_sql($query2, $pdo);

    foreach ($array as $row) {
        $rate = $row['rate'];
    }

    db_close($pdo);
    ?>
</head>

<body>
    good luck to you<br />
    <div id="now_usdjpy" class="usdjpy_value"></div>
    <div id="margin" class="usdjpy_value"></div>
    <div id="swap_point" class="usdjpy_value"></div>
    <div id="loser_value" class="loser_value"></div>

    <script>
        let rate = ('<?php echo $rate ?>');
        console.log("exchange rate:", parseFloat(rate).toFixed(3));

        let baseValue = 129.2479;
        let nowDiff = baseValue - parseFloat(rate);
        let lot = 400;
        let liabilities = (nowDiff * lot * 10000).toFixed(0);
        let deposit = 56080;
        let swap = -89;
        let margin = deposit * lot;

        var loadDate = new Date();
        var distDate = new Date(2022, 5, 10);
        var diffMilliSec = loadDate - distDate;
        let swapDate = parseInt(diffMilliSec / 1000 / 60 / 60 / 24);
        console.log("liabilities:", liabilities);

        document.getElementById("now_usdjpy").innerText = "USDJPY:" + parseFloat(rate).toFixed(3);
        document.getElementById("margin").innerText = "margin:" + margin.toLocaleString();
        document.getElementById("swap_point").innerText = "SELL-swap:" + (lot * swap * swapDate).toLocaleString();
        document.getElementById("loser_value").innerText = "🐼 is " + Math.floor(liabilities).toLocaleString();
    </script>
</body>

</html>