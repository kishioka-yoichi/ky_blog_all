@section('title')
    OMOIDay 
@endsection

@extends('layouts.mainapp')

@section('content')
<div id="dairyCreate_frame">
    {{--  フォームのactionを /dairyCreatePost に修正（Controller修正を反映） --}}
    <form action="/dairyCreate" method="post" enctype="multipart/form-data">
        @csrf
        <div id="dairyCreate_main">
            <div id="dairyCreate_input">
                <?php
                    $title = "\"{$inputArray["title"]}\"";
                    $other = $inputArray["other"];
                    $inputNull = $inputArray["inputNull"];
                    $imageSizeOver = $inputArray["imageSizeOver"];
                    
                    echo "<div><tr><th>タイトル:</th><td><input type=\"text\" name=\"title\" value=$title></td></tr></div>";
                    echo "<div><tr><th>内容　　:</th><td><textarea name=\"content\" id=\"textareaContent\" cols=\"50\" rows=\"7\"></textarea></td></tr></div>";
                    echo "<div><tr><th>画像　　:<input type=\"file\" name=\"image\"></td></tr></div>";
                ?>
                
                {{-- ---------------------------------------------------- --}}
                {{-- 💡 ここから新しい場所・位置情報入力エリアに置き換え --}}
                {{-- ---------------------------------------------------- --}}
                
                <hr style="margin: 15px 0;">
                
                {{-- 1. 選択モードのラジオボタン --}}
                <div class="location-mode-selection">
                    <div><tr><th>場所の入力方法:</th><td>
                        <label>
                            <input type="radio" name="location_mode" value="auto" id="mode_auto" checked>
                            自動取得（地図表示あり）
                        </label><br>
                        <label>
                            <input type="radio" name="location_mode" value="manual" id="mode_manual">
                            手動入力（位置情報なし）
                        </label>
                    </td></tr></div>
                </div>

                {{-- 2. モード「自動取得」用のエリア --}}
                <div id="auto_location_area">
                    <div><tr><th>位置情報:</th><td><span id="locationStatus">状態: 取得待ち...</span></td></tr></div>
                    <div><tr><th></th><td><button type="button" id="get_location_button">現在地を取得</button></td></tr></div>
                    
                    {{-- 実際にコントローラーに送るための隠しフィールド --}}
                    <input type="hidden" name="latitude" id="input_lat" value="">
                    <input type="hidden" name="longitude" id="input_lng" value="">
                </div>

                {{-- 3. モード「手動入力」用のエリア --}}
                <div id="manual_location_area" style="display: none;">
                    <div><tr><th>場所（自由入力）:</th><td>
                        <input type="text" name="manual_place" id="manual_place_input" placeholder="例：カフェA店">
                    </td></tr></div>
                </div>
                
                <hr style="margin: 15px 0;">
                
                <?php
                    // ... (エラーメッセージの表示) ...
                    if ($other !== "") {
                        echo "<p>{$other}</p>";
                    }
                    if ($inputNull) {
                        echo "<p>※全箇所入力してください</p>";
                    }
                    if ($imageSizeOver) {
                        echo "<p>※画像は16MB以下でないといけません</p>";
                    }
                ?>

                <hr style="margin: 15px 0;">
                <h3>🌐 公開設定</h3>
                <div>
                    <div><tr><th>公開範囲:</th><td>
                        <label>
                            <input type="radio" name="is_public" value="1" checked>
                            公開する（みんなの投稿に表示）
                        </label><br>
                        <label>
                            <input type="radio" name="is_public" value="0">
                            非公開にする（自分だけが見る）
                        </label>
                    </td></tr></div>
                </div>
                <hr style="margin: 15px 0;">
                
            </div>
            <tr><td><input class="insert_button" type="submit" name="send" value="投稿"></td></tr>
        </div>
    </form>
</div>

{{-- ---------------------------------------------------- --}}
{{-- 💡 既存のJavaScriptを新しいロジックに置き換え --}}
{{-- ---------------------------------------------------- --}}
<script>
    <?php
        // 既存の content の復元ロジックは残す
        $content = "{$inputArray["content"]}";
    ?>
    const textarea = document.querySelector('textarea[name="content"]');
    textarea.value = "<?php echo $content; ?>"; 

document.addEventListener('DOMContentLoaded', () => {
    // DOM要素の取得
    const autoArea = document.getElementById('auto_location_area');
    const manualArea = document.getElementById('manual_location_area');
    const statusText = document.getElementById('locationStatus');
    const getLocationButton = document.getElementById('get_location_button');
    const inputLat = document.getElementById('input_lat');
    const inputLng = document.getElementById('input_lng');
    const radioAuto = document.getElementById('mode_auto');
    const radioManual = document.getElementById('mode_manual');
    
    // --- 選択モードの切り替え処理 ---
    function toggleLocationMode(mode) {
        if (mode === 'auto') {
            autoArea.style.display = 'block';
            manualArea.style.display = 'none';
            // 自動モードで取得済みでなければ状態をリセット
            if (inputLat.value === '') {
                 statusText.textContent = '状態: 取得待ち...';
                 statusText.style.color = 'black';
            }
        } else {
            autoArea.style.display = 'none';
            manualArea.style.display = 'block';
            // 手動モードでは位置情報をクリア
            inputLat.value = '';
            inputLng.value = '';
        }
    }

    // ラジオボタンの変更イベント
    radioAuto.addEventListener('change', () => toggleLocationMode('auto'));
    radioManual.addEventListener('change', () => toggleLocationMode('manual'));

    // 初期状態のセット
    toggleLocationMode(radioAuto.checked ? 'auto' : 'manual');


    // --- 位置情報取得処理 ---
    getLocationButton.addEventListener('click', () => {
        statusText.textContent = '状態: GPSを探索中...';
        statusText.style.color = 'orange';
        getLocationButton.disabled = true;
        inputLat.value = ''; // リセット
        inputLng.value = ''; // リセット

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // 成功時の処理
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    inputLat.value = lat;
                    inputLng.value = lng;
                    statusText.textContent = `取得完了 (緯度:${lat.toFixed(6)}, 経度:${lng.toFixed(6)})`;
                    statusText.style.color = 'green';
                    getLocationButton.disabled = false;
                },
                (error) => {
                    // 失敗時（ユーザー拒否など）の処理
                    let message;
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = "拒否されました。手動入力に切り替えるか再試行してください。";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = "利用できません。";
                            break;
                        case error.TIMEOUT:
                            message = "タイムアウトしました。";
                            break;
                        default:
                            message = "不明なエラーが発生しました。";
                    }
                    statusText.textContent = `状態: 取得失敗 (${message})`;
                    statusText.style.color = 'red';
                    getLocationButton.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
            );
        } else {
            statusText.textContent = '状態: このブラウザは位置情報に対応していません。';
            statusText.style.color = 'gray';
            getLocationButton.disabled = false;
        }
    });

    // 初期ロード時に自動取得モードであれば位置情報取得を試みる
    if(radioAuto.checked) {
        getLocationButton.click(); 
    }
});
</script>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
@endsection