<?php
include "db_write.php";
if (isset($_POST['add']) && isset($_POST['comment'])) {
    date_default_timezone_set('Asia/Tokyo');
    $unitime = date('Y-m-d H:i:s');

    $pdo = db_access();
    $query = "INSERT INTO loser_comment (id, comment, updatetime) VALUES (NULL, '" . $_POST['comment'] . "', '" . $unitime . "')";
    db_prepare_sql($query, $pdo);
    db_close($pdo);
    header('Location: ./');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./style.css" />
    <title>real time loser checker</title>
</head>

<body>
    <header>
        <h1>THE LOSER CHECKER&copy;</h1>
    </header>
    <table class="rate">
        <tr>
            <td>USD/JPY</td>
            <td id="usdjpyrate" class="rateup ratedown flashing"></td>
        </tr>
        <tr>
            <td>GBP/JPY</td>
            <td id="gbpjpyrate" class="rateup ratedown flashing"></td>
        </tr>
        <tr>
            <td>EUR/JPY</td>
            <td id="eurjpyrate" class="rateup ratedown flashing"></td>
        </tr>
        <tr>
            <td>AUD/JPY</td>
            <td id="audjpyrate" class="rateup ratedown flashing"></td>
        </tr>
        <tr>
            <td>XAU/USD</td>
            <td id="xauusdrate" class="rateup ratedown flashing"></td>
        </tr>
    </table>
    <div class="main_wrapper">
        <h2>Good Luck to You</h2>
        <div class="left_box">
            <div class="message_box">
                <form action="index.php" method="post">
                    <input type="text" name="comment">
                    <button type="submit" name="add" maxlength="100">ADD</button>
                </form>
            </div>
            <?php
            $pdo = db_access();
            $query = "SELECT * FROM loser_comment ORDER BY loser_comment.id DESC LIMIT 100";
            $result = db_prepare_sql($query, $pdo);
            db_close($pdo);

            echo "<div class='comment_wrapper'>";
            foreach ($result as $row) {
                echo "<div class='comment_box'>";
                echo "<p class='comment'>" . $row['comment'] . "</p>";
                echo "<p class='comment_time'>" . $row['updatetime'] . "</p>";
                echo "</div>";
            }
            echo "</div>";
            ?>
        </div>
        <div class="right_box">
            <div class="equity">耐久値 <label id="equity"></label></div>
            <div class="nextlevel">次のレベルまで <label id="nextlevel"></label></div>
            <div class="traderLevel">LEVEL <label id="traderLevel"></label></div>
            <div class="previouslevel">あと <label id="previouslevel"></label> でレベルダウン</div>
            <div class="previouslevel">あと <label id="previouslevel2"></label> でレベル2ダウン</div>
        </div>
    </div>
    <footer>
        <p id="updatetime"></p>
    </footer>

    <script>
        let tmp_js_array;
        // exchange rate
        const id_usdjpy = document.getElementById('usdjpyrate');
        const id_gbpjpy = document.getElementById("gbpjpyrate");
        const id_eurjpy = document.getElementById("eurjpyrate");
        const id_audjpy = document.getElementById("audjpyrate");
        const id_xauusd = document.getElementById("xauusdrate");

        // user infomation
        const id_equity = document.getElementById("equity");
        const id_level = document.getElementById("traderLevel");
        const id_nextlevel = document.getElementById("nextlevel");
        const id_previouslevel = document.getElementById("previouslevel");
        const id_previouslevel2 = document.getElementById("previouslevel2");

        const id_updatetime = document.getElementById("updatetime");
        const experience_table = [
            0, 10000, 11000, 12100, 13310, 14641, 16105, 17716, 19487, 21436,
            23579, 25937, 28531, 31384, 34523, 37975, 41772, 45950, 50545, 55599,
            61159, 67275, 74002, 81403, 89543, 98497, 108347, 119182, 131100, 144210,
            158631, 174494, 191943, 211138, 232252, 255477, 281024, 309127, 340039, 374043,
            411448, 452593, 497852, 547637, 602401, 662641, 728905, 801795, 881975, 970172,
            1067190, 1173909, 1291299, 1420429, 1562472, 1718719, 1890591, 2079651, 2287616, 2516377,
            2768015, 3044816, 3349298, 3684228, 4052651, 4457916, 4903707, 5394078, 5933486, 6526834,
            7179518, 7897470, 8687217, 9555938, 10511532, 11562685, 12718954, 13990849, 15389934, 16928927,
            18621820, 20484002, 22532402, 24785643, 27264207, 29990628, 32989690, 36288659, 39917525, 43909278,
            48300206, 53130226, 58443249, 64287574, 70716331, 77787964, 85566760, 94123437, 103535780
        ];

        const recieve_postdata = () => {
            let promise = fetch('http://localhost/fxrategetter/json.php');
            let result = promise.then(response => response.json())
                .then(data => {
                    show_rate(data);
                });
        }

        function level_check(equity_maney) {
            let level = 0;
            for (var item in experience_table) {
                if (experience_table[item] >= equity_maney) {
                    id_nextlevel.innerHTML = (experience_table[item] - equity_maney).toLocaleString();
                    id_level.innerHTML = (level);
                    id_previouslevel.innerHTML = (equity_maney - experience_table[item - 1]).toLocaleString();
                    id_previouslevel2.innerHTML = (equity_maney - experience_table[item - 2]).toLocaleString();
                    break;
                }
                level++;
            }
        }

        function show_rate(data) {
            let js_text = JSON.stringify(data);
            let js_array = JSON.parse(js_text);

            if (tmp_js_array != null) {
                // 前回値との差分から上昇か下降かを決める
                for (new_rate in js_array) {
                    let diff = tmp_js_array[new_rate] - js_array[new_rate];

                    if (diff == 0) {
                        style_change(new_rate, diff);
                    } else if (diff > 0) {
                        style_change(new_rate, diff);
                    } else if (diff < 0) {
                        style_change(new_rate, diff);
                    }
                }
            }

            id_usdjpy.innerHTML = parseFloat(js_array["USDJPY"]).toFixed(3);
            id_gbpjpy.innerHTML = parseFloat(js_array["GBPJPY"]).toFixed(3);
            id_eurjpy.innerHTML = parseFloat(js_array["EURJPY"]).toFixed(3);
            id_audjpy.innerHTML = parseFloat(js_array["AUDJPY"]).toFixed(3);
            id_xauusd.innerHTML = parseFloat(js_array["XAUUSD"]).toFixed(2);
            id_equity.innerHTML = parseFloat(js_array["Equity"]).toLocaleString();
            id_updatetime.innerHTML = "updatetime:" + js_array["updatetime"];

            level_check(js_array["Equity"]);

            tmp_js_array = js_array;
        }

        function style_change(new_rate, diff) {
            switch (new_rate) {
                case "USDJPY":
                    arrow_change(id_usdjpy, diff);
                    break;
                case "GBPJPY":
                    arrow_change(id_gbpjpy, diff);
                    break;
                case "EURJPY":
                    arrow_change(id_eurjpy, diff);
                    break;
                case "AUDJPY":
                    arrow_change(id_audjpy, diff);
                    break;
                case "XAUUSD":
                    arrow_change(id_xauusd, diff);
                    break;
                default:
                    break;

            }
        }

        function arrow_change(currency, diff) {
            if (diff == 0) {
                currency.classList.remove("ratedown");
                currency.classList.remove("rateup");
                currency.classList.remove("flashing");
                currency.style.color = "#76FF03";
            } else if (diff < 0) {
                currency.classList.remove("ratedown");
                currency.classList.add("rateup");
                currency.classList.add("flashing");
                currency.style.color = "#FF8A65";
            } else if (diff > 0) {
                currency.classList.add("ratedown");
                currency.classList.remove("rateup");
                currency.classList.add("flashing");
                currency.style.color = "#29B6F6";
            }
        }

        setInterval(recieve_postdata, 1000);

        function show_loser(loser, currency, rate, updatetime, position, lot, deposit = 0, swap = 0, possession = 0, distDate = 0) {
            let pips = (position - parseFloat(rate)) * 100; // pips
            let liabilities = (pips * lot * 100).toFixed(0); // 含み損
            let margin = deposit * lot; // 保証金
            let losscut_rate = parseFloat(rate) + ((possession + parseFloat(liabilities) - (margin / 2)) / (lot * 10000)); // ロスカットレート

            var loadDate = new Date();
            var diffMilliSec = loadDate - distDate;
            let swapDate = parseInt(diffMilliSec / 1000 / 60 / 60 / 24); // 取引期間（日）

            var array = [
                loser,
                currency,
                position,
                lot,
                pips,
                Math.floor(liabilities).toLocaleString()
            ];
        }
    </script>


</body>

</html>