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
            margin:0;
            padding:0;
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
            width:100%;
        }

        .rate{
            background:#263238;
            color:#FF7043;
            border:0;
        }

        .rate tr{
            width:calc(20vw - 2px);
            border: solid 1px #999;
            float:left;
            border-collapse: collapse;
        }

        .rate td{
            animation: flash 0.3s linear infinite;
            text-align:center;
            width:50%;
            float:left;
            margin:10px 0px;
        }

        @keyframes flash {
  0%,100% {
    opacity: 1;
  }

  50% {
    opacity: 0;
  }
}
    </style>
    <?php
    include("./db_write.php");

    $pdo = db_access();

    // JSON.phpからデータ取得
    $query = 'SELECT * FROM exchange_rate';
    $array = db_prepare_sql($query, $pdo);
    $php_array = array();

    foreach ($array as $row) {
        $currency = trim($row['currency']);
        $rate = trim($row['rate']);
        $updatetime = $row['updatetime'];

        $php_array = $php_array + array($currency => $rate);
    }

    $json_array = json_encode($php_array, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    db_close($pdo);
    ?>
</head>

<body>
    <table class="rate">
        <tr><td>USD/JPY</td><td id="usdjpyrate"></td></tr>
        <tr><td>GBP/JPY</td><td id="gbpjpyrate"></td></tr>
        <tr><td>EUR/JPY</td><td id="eurjpyrate"></td></tr>
        <tr><td>AUD/JPY</td><td id="audjpyrate"></td></tr>
        <tr><td>XAU/USD</td><td id="xauusdrate"></td></tr>
    </table>
    <p id="updatetime"></p>
    good luck to you<br />
    <script>
        const promise = fetch('http://localhost/fxrategetter/json.php');
        let tmprate;

        let result = promise.then(response => response.json())
            .then(data => {
                show_rate(data);
                setInterval('show_rate(data)',1000);
            });

        function show_rate(data) {
            let js_text = JSON.stringify(data);
            let js_array = JSON.parse(js_text);

            for (new_rate in js_array) {
                console.log(new_rate, js_array[new_rate]);
            }

            document.getElementById("usdjpyrate").innerHTML = js_array["USD/JPY"];
            document.getElementById("gbpjpyrate").innerHTML = js_array["GBP/JPY"];
            document.getElementById("eurjpyrate").innerHTML = js_array["EUR/JPY"];
            document.getElementById("audjpyrate").innerHTML = js_array["AUD/JPY"];
            document.getElementById("xauusdrate").innerHTML = js_array["XAU/USD"];
            document.getElementById("updatetime").innerHTML = js_array["updatetime"];

            tmprate = js_array;
        }



        /*
        let rate = json_array["USDJPY"];
        let updateTime = json_array["datetime"];

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


        let baseValue2 = 144.299;
        let nowDiff2 = baseValue2 - parseFloat(rate);
        let lot2 = 270;
        let liabilities2 = (nowDiff2 * lot2 * 10000).toFixed(0);
        let deposit2 = 22800;
        let swap2 = -123;
        let margin2 = deposit2 * lot2;
        let possession2 = 57026247;
        let losscut_rate2 = parseFloat(rate) + ((possession2 + parseFloat(liabilities2) - 5700000.0) / 5000000.0);

        var distDate2 = new Date(2022, 8, 13); // real 22/9/13
        var diffMilliSec2 = loadDate - distDate2;
        let swapDate2 = parseInt(diffMilliSec2 / 1000 / 60 / 60 / 24);

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
        */
    </script>

    <script language="JavaScript" type="text/javascript">
        posNum = 7;

        document.write('<img src="cts/counter_s.php?pos=0" width="0" height="0">');
        for (i = posNum; i > 0; i--) {
            document.write('<img src="cts/counter_s.php?pos=' + i + '">');
        }
    </script>

    <form action="" method="post">
        <select name="losername">
            <option value="1">🐼</option>
            <option value="2">FSKB</option>
        </select>
        <input type="text" value="">
        <input type="text" value="">
        <input type="submit" value="送信"><input type="reset" value="リセット">
    </form>

</body>

</html>