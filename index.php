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

        .loser_value,
        .loser_value2 {
            font-size: 5em;
            font-weight: bold;
            color: red;
        }

        table {
            border: solid 1px #999;
            border-collapse: collapse;
            text-align: center;
            margin: 10px auto;
        }

        td {
            border: solid 1px #999;

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

    date_default_timezone_set('Asia/Tokyo');
    $unitime = date('Y-m-d H:i:s');

    if (isset($_POST['value2'])) {
        $value2 = '';
        $value2 = $_POST['value2'];
        $value2 = htmlspecialchars($value2);
        $value2 = stripslashes($value2);

        $echo_str = $echo_str . "$value2" . "\n";
        $query1 = 'UPDATE exchange_rate SET rate = "' . $value2 . '", updatetime = "' . $unitime . '" WHERE exchange_rate.id = 1';
        $result = db_prepare_sql($query1, $pdo);
    }



    $query2 = 'SELECT * FROM exchange_rate WHERE id = 1';
    $array = db_prepare_sql($query2, $pdo);

    foreach ($array as $row) {
        $rate = $row['rate'];
        $updatetime = $row['updatetime'];
    }

    db_close($pdo);
    ?>
</head>

<body>
    good luck to you<br />
    <script>
        console.log("ok");
        let rate = ('<?php echo $rate ?>');
        let updateTime = ('<?php echo $updatetime ?>');

        console.log("ok");
        let baseValue = 143.968;
        let nowDiff = baseValue - parseFloat(rate);
        let lot = 100;
        let liabilities = (nowDiff * lot * 10000).toFixed(0);
        let deposit = 56080;
        let swap = -89;
        let margin = deposit * lot;
        let possession = 40000000;
        let losscut_rate = parseFloat(rate) + ((possession + parseFloat(liabilities) - (margin / 2)) / 4000000.0);

        var loadDate = new Date();
        var distDate = new Date(2022, 8, 13); // real 22/9/13
        var diffMilliSec = loadDate - distDate;
        let swapDate = parseInt(diffMilliSec / 1000 / 60 / 60 / 24);


        let baseValue2 = 142.369;
        let nowDiff2 = baseValue2 - parseFloat(rate);
        let lot2 = 400;
        let liabilities2 = (nowDiff2 * lot2 * 10000).toFixed(0);
        let deposit2 = 22800;
        let swap2 = -123;
        let margin2 = deposit2 * lot2;
        let possession2 = 57026247;
        let losscut_rate2 = parseFloat(rate) + ((possession2 + parseFloat(liabilities2) - 5700000.0) / 5000000.0);

        var distDate2 = new Date(2022, 8, 9); // real 22/8/24
        var diffMilliSec2 = loadDate - distDate2;
        let swapDate2 = parseInt(diffMilliSec2 / 1000 / 60 / 60 / 24);

        console.log(loadDate);
        console.log(distDate);
        console.log(distDate2);

        var array = [];
        array[0] = [
            "loser name",
            "資金",
            "所持ロット",
            "必要証拠金",
            "必要証拠金合計",
            "スワップAVG",
            "スワップ合計",
            "所持通貨平均レート",
            "所持通貨現在レート",
            "損失",
            "予想ロスカットレート"
        ];

        array[1] = [
            "🐼",
            (possession).toLocaleString(),
            lot,
            (deposit).toLocaleString(),
            (margin).toLocaleString(),
            swap,
            (swap * lot * swapDate).toLocaleString(),
            baseValue,
            parseFloat(rate).toFixed(3),
            Math.floor(liabilities).toLocaleString(),
            parseFloat(losscut_rate).toFixed(3)
        ];

        array[2] = [
            "FKSB",
            possession2.toLocaleString(),
            lot2,
            (deposit2).toLocaleString(),
            (margin2).toLocaleString(),
            swap2,
            (swap2 * lot2 * swapDate2).toLocaleString(),
            baseValue2,
            parseFloat(rate).toFixed(3),
            Math.floor(liabilities2).toLocaleString(),
            parseFloat(losscut_rate2).toFixed(3)
        ];
        

        document.write("<table>");
        for (i = 0; i < array.length; i++) {
            document.write("<tr>");
            for (j = 0; j < 11; j++) {
                document.write("<td>" + array[i][j] + "</td>");
            }
            document.write("</tr>");
        }
        document.write("</table>");
        document.write("更新時間 " + updateTime + "<br />");
    </script>

    <script language="JavaScript" type="text/javascript">
        posNum = 7;

        document.write('<img src="cts/counter_s.php?pos=0" width="0" height="0">');
        for (i = posNum; i > 0; i--) {
            document.write('<img src="cts/counter_s.php?pos=' + i + '">');
        }
    </script>
</body>

</html>