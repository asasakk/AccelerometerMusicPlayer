<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.1.2/howler.min.js"></script>
    <title>CAROLLIN</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bakbak+One&family=Zen+Maru+Gothic:wght@500&display=swap');

    body {
        font-family: 'Bakbak One', sans-serif;
        overflow: hidden;
        margin: 0;
        padding: 0;
        background-image: url('../css/img/back.png');
        background-size: 100% 100%;
        background-position: center center;
        background-repeat: no-repeat;
        height: 100vh;
    }

    h1 {
        padding-top: 90px;
        padding-bottom: 30px;
        width: 90%;
        box-sizing: border-box;
        text-align: center;
        margin: 0 auto;
    }

    h1 img {
        display: block;
        width: 100%;
        height: auto;
    }

    h2 {
        display: flex;
        justify-content: center;
    }

    h2 img {
        width: 50%;
        height: auto;
    }

    p {
        color: #D8AE18;
        position: absolute;
        padding-top: 50%;
        padding-bottom: 5%;
        top: 2%;
        left: 11%;
        font-size: 11rem;
        font-family: "Showcard Gothic";
        src: url("../css/SHOWG.TTF") format("truetype");
        text-shadow:
            3px 3px 3px #000, -3px -3px 3px #000,
            -3px 3px 3px #000, 3px -3px 3px #000;
    }
</style>

<body>

    <p id="timer"></p>

    <div class="input-group input-group-sm">
        <input id="acc-z" class="form-control"></input>
    </div>

    <h1><img src="../css/img/santa_second.png"></h1>

    <script>
        const TIME = 7;
        let m_cnt = 0;
        let o_cnt = 0;
        let eff; // Howlerオブジェクト
        var sound;

        // URLからクエリパラメータを取得
        const urlParams = new URLSearchParams(window.location.search);

        // 'id'の値を取得
        window.number = urlParams.get('id'); // グローバル変数として定義

        // 画像データをlocalStorageから読み込み、再度localStorageに保存
        var imageData = localStorage.getItem('imageData');
        localStorage.setItem('imageData', imageData);

        // 加速度センサの使用が許可されているかの確認
        $(function () {
            init();
        });

        let init = () => {
            if (window.DeviceOrientationEvent) {
                if (DeviceOrientationEvent.requestPermission && typeof DeviceOrientationEvent.requestPermission === 'function') {
                    $('#cdiv').css('display', 'none');
                    let banner = '<div id="sensorrequest" onclick="ClickRequestDeviceSensor();" class="d-grid container mt-4"><div class="btn btn-warning">センサーの有効化</div></div>';
                    $('body').prepend(banner);
                } else {
                    window.addEventListener("devicemotion", deviceMotion);
                    // window.addEventListener("deviceorientation", deviceOrientation);
                }
            }
        }

        // ユーザーにセンサを使用する「許可」ボタンを押したときの処理
        let ClickRequestDeviceSensor = () => {
            // デバイスの加速度の取得が許可されたときの処理
            DeviceMotionEvent.requestPermission().then(function (response) {
                if (response === 'granted') {
                    window.addEventListener("devicemotion", deviceMotion);
                    $('#sensorrequest').css('display', 'none');
                    $('#cdiv').css('display', 'block');
                }
            }).catch(function (e) {
                console.log(e);
            });
        }

        // 加速度センサーの値に基づいて音量とバーを制御する関数
        function updateVolumeAndBarByAcceleration(xValue) {
            // 加速度の絶対値を0から1の範囲に正規化
            var normalizedAccX = normalizeValue(Math.abs(xValue), 0, 20); // 必要に応じて範囲を調整
            // Howler.jsで音量を設定
            sound.volume(normalizedAccX);
            // バーの値を更新
        }

        function normalizeValue(value, min, max) {
            return (value - min) / (max - min);
        }

        // 加速度の取得
        let deviceMotion = (e) => {
            e.preventDefault();
            if (TIME == m_cnt) { // 表示間隔調整
                let acc = e.acceleration;
                let xy = (acc.x + acc.y) / 2
                InsertValue("acc-x", acc.x);
                InsertValue("acc-y", acc.y);
                InsertValue("acc-z", xy);
                // 音量とバーの更新
                updateVolumeAndBarByAcceleration(xy);
                m_cnt = 0;
            }
            m_cnt++;
        }

        // センサから取得した値をフォームに表示する
        let InsertValue = (element_id, value) => {
            $("#" + element_id).val(value);
        }

        document.addEventListener('DOMContentLoaded', function () {
            sound = new Howl({
                src: [`../music/sounds_${window.number}.wav`],
                onend: function () {
                    setTimeout(function () {
                        window.location.href = 'F8.php';
                    }, 3000);
                }
            });
        });

        // 外部HTMLファイルのパス
        const externalFilePath = 'F7.php';

        // 一定の時間ごとに外部HTMLを読み込む関数
        function loadExternalHTML() {
            const contentContainer = document.getElementById('contentContainer');
            // XMLHttpRequestを使用して外部HTMLを非同期で読み込む
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // 以前のHTMLを完全に削除
                    document.body.innerHTML = xhr.responseText;
                }
            };
            // 非同期で取得
            xhr.open('GET', externalFilePath, true);
            xhr.send();
        }

        function updateTimer() {
            // 現在の時間を取得する
            const now = new Date();
            const second = now.getSeconds();
            // 秒だけ表示する
            document.getElementById("timer").innerHTML = `${60 - second < 10 ? "0" : ""}${60 - second === 60 ? 0 : 60 - second}`;
            // 次の秒になったら指定のURLに飛ぶ
            if (second === 0) {
                loadExternalHTML();
                sound.play();
            }
        }

        // 最初に1回呼び出し、その後1秒ごとに繰り返し呼び出す
        updateTimer();
        setInterval(updateTimer, 1000);

    </script>

</body>

</html>
