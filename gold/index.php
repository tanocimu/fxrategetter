<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gold get chance</title>
</head>

<body>
    <div>
        位置情報を利用して、宝探しを始めましょう！
    </div>

    <script>
        get_location();

        function getParam(name, url = 0) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        function get_location() {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    let locationInfo = [];
                    locationInfo.push(getParam("user"));
                    locationInfo.push(position.coords.latitude + "," + position.coords.longitude);
                    //locationInfo.push(new Date(position.timestamp).toLocaleString());

                    let geo_text = "";
                    for (let index = 0; index < locationInfo.length; index++) {
                        geo_text += locationInfo[index] + "<br />";
                    }

                    let request = new XMLHttpRequest();
                    let location_url = "./db_write.php?user=" + locationInfo[0] + "&location=" + locationInfo[1];

                    request.open('GET', location_url, true);
                    request.responseType = 'json';
                    request.addEventListener('load', function(response) {
                        var data = <?= "this.response" ?>;
                        console.log("result", data);
                    });
                    request.send();

                    // document.getElementById('position_view').innerHTML = geo_text;
                },
                function(error) {
                    switch (error.code) {
                        case 1: //PERMISSION_DENIED
                            alert("位置情報の利用が許可されていません");
                            break;
                        case 2: //POSITION_UNAVAILABLE
                            alert("現在位置が取得できませんでした");
                            break;
                        case 3: //TIMEOUT
                            alert("タイムアウトになりました");
                            break;
                        default:
                            alert("その他のエラー(エラーコード:" + error.code + ")");
                            break;
                    }
                }
            );

        }
    </script>
</body>

</html>