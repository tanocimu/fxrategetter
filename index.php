<?php
    include "db_write.php";
    if (isset($_POST['add']) && isset($_POST['comment'])) {
        $pdo = db_access();
        $query = "INSERT INTO loser_comment (id, comment, updatetime) VALUES (NULL, '" . $_POST['comment'] . "', current_timestamp())";
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
                echo "<p class='comment'>".$row['comment']."</p>";
                echo "<p class='comment_time'>".$row['updatetime']."</p>";
                echo "</div>";
            }
            echo "</div>";
            ?>
        </div>
        <div class="right_box">no information</div>
    </div>
    <footer>
        <p id="updatetime"></p>
        <script language="JavaScript" type="text/javascript">
            posNum = 7;

            document.write('<img src="cts/counter_s.php?pos=0" width="0" height="0">');
            for (i = posNum; i > 0; i--) {
                document.write('<img src="cts/counter_s.php?pos=' + i + '">');
            }
        </script>
    </footer>

    <script>
        let tmp_js_array;
        const id_usdjpy = document.getElementById('usdjpyrate');
        const id_gbpjpy = document.getElementById("gbpjpyrate");
        const id_eurjpy = document.getElementById("eurjpyrate");
        const id_audjpy = document.getElementById("audjpyrate");
        const id_xauusd = document.getElementById("xauusdrate");
        const id_updatetime = document.getElementById("updatetime");

        const recieve_postdata = () => {
            let promise = fetch('http://localhost/fxrategetter/json.php');
            let result = promise.then(response => response.json())
                .then(data => {
                    show_rate(data);
                });
        }

        function show_rate(data) {
            let js_text = JSON.stringify(data);
            let js_array = JSON.parse(js_text);

            /*
            for (new_rate in js_array) {
                console.log(new_rate, js_array[new_rate]);
            }
            */
            if (tmp_js_array != null) {
                // 前回値との差分から上昇か下降かを決める
                for (new_rate in js_array) {
                    //let diff = tmp_js_array[new_rate] - js_array[new_rate];
                    let diff = 0;
                    if (diff == 0) {
                        style_change(new_rate, diff);
                    } else if (diff > 0) {
                        style_change(new_rate, diff);
                    } else if (diff < 0) {
                        style_change(new_rate, diff);
                    }
                }
            }

            id_usdjpy.innerHTML = js_array["USDJPY"];
            id_gbpjpy.innerHTML = js_array["GBPJPY"];
            id_eurjpy.innerHTML = js_array["EURJPY"];
            id_audjpy.innerHTML = js_array["AUDJPY"];
            id_xauusd.innerHTML = js_array["XAUUSD"];
            id_updatetime.innerHTML = "updatetime:" + js_array["updatetime"];

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
            } else if (diff > 0) {
                currency.classList.remove("ratedown");
                currency.classList.add("rateup");
                currency.classList.add("flashing");
                currency.style.color = "#FF8A65";
            } else if (diff < 0) {
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