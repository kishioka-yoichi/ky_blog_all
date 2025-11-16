@extends('layouts.mainapp')

@section('content')
    <div id="dairyLog_content">
        @php
            $count = count($dairys);
            $lastDairy = $count > 0 ? $dairys[0] : null; // 最新の日記は配列の最初の要素 (getPublicDiariesで降順ソートしているため)

            if ($lastDairy) {
                $last_title = $lastDairy['title'];
                $last_content = $lastDairy['content'];
                $last_created_at = (new DateTime($lastDairy['created_at']))->format('Y-m-d H:i:s');  // Eloquentから取得した日付は文字列ではなくDateTimeオブジェクトとして渡されます
                $last_newContent = str_replace("\r\n", "<br>", $last_content);
                
                $last_image = $lastDairy['image'] ?? null;
                $last_imageText = '';

                if (!empty($last_image)) {
                    // DBから取得したBase64文字列を整形
                    // 画像の形式がPNG以外の場合は、'image/png' を 'image/jpeg' などに修正してください
                    $last_imageText = "data:image/png;base64," . $last_image;
                }

                $last_latitude = $lastDairy['latitude'] ?? '位置情報なし';
                $last_longitude = $lastDairy['longitude'] ?? ''; // 緯度と合わせて表示するため空でもOK
                $last_locationText = $lastDairy['latitude'] ? "緯度: {$last_latitude}, 経度: {$last_longitude}" : '位置情報なし';
                $last_place = $lastDairy['place'] ?? '場所なし';

                $last_userName = $lastDairy['user_name']; // user_name を投稿者名として使用
            }
        @endphp

        <div id="dairyLog_tab">
            <p>一覧</p>
            @foreach ($dairys as $dairy)
            <?php
                $title = "\"{$dairy["title"]}\"";
                $content = $dairy["content"];
                $formatted_created_at = (new DateTime($dairy["created_at"]))->format('Y-m-d H:i:s');
                $created_at = "\"{$formatted_created_at}\"";
                $newContent = str_replace("\r\n", "<br>", $content);
                $newContentText = "\"$newContent\"";
                $image = $dairy["image"];
                $imageText = "";
                if (!empty($image)) {
                    // DBに保存されているBase64文字列をそのまま使用
                    // 注意: 保存時にMIMEタイプを特定できていないため、ここでは'png'と仮定
                    $imageText = "data:image/png;base64," . $image;
                }
                $latitude = $dairy["latitude"] ?? 'null'; 
                $longitude = $dairy["longitude"] ?? 'null';
                $locationData = $latitude != 'null' ? "\"緯度: {$latitude}, 経度: {$longitude}\"" : "\"位置情報なし\"";
                $place = $dairy["place"] ?? '場所なし';
                $placeData = "\"{$place}\""; 
                $rawUserName = $dairy["user_name"]; 
                $userName = "\"{$rawUserName}\"";  // user_name を取得
                $userID = $dairy["user_id"]; 
                $example = "\"AAA\r\nBBB\"";
                echo "<div><input class=\"dairyLog_tab_button\" type=\"button\" value=\"投稿者: {$rawUserName} 日付: {$formatted_created_at}\" onclick='
                    document.getElementById(\"dairyLog_main_createdAtContent\").textContent=$created_at;
                    document.getElementById(\"dairyLog_main_titleContent\").textContent=$title;
                    document.getElementById(\"dairyLog_main_contentContent\").innerHTML=$newContentText; 
                    document.getElementById(\"dairyLog_main_imageContent\").src=\"$imageText\";
                    document.getElementById(\"dairyLog_main_locationContent\").textContent=$locationData;
                    document.getElementById(\"dairyLog_main_placeContent\").textContent=$placeData;
                    
                    document.getElementById(\"dairyLog_main_locationContent\").setAttribute(\"data-lat\", \"$latitude\");
                    document.getElementById(\"dairyLog_main_locationContent\").setAttribute(\"data-lng\", \"$longitude\");
                    document.getElementById(\"dairyLog_main_userIDContent\").textContent=$userName; // ユーザー名の表示更新

                updateMap($latitude, $longitude); 
                
                '></div>";
            ?>
            @endforeach
        </div>
        
        <div id="dairyLog_main">
            @if ($lastDairy)
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">投稿者　　:</p>
                    <p id="dairyLog_main_userIDContent">{{ $last_userName }}</p> 
                </div>
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">日付　　　:</p>
                    <p id="dairyLog_main_createdAtContent">{{ $last_created_at }}</p> 
                </div>
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">タイトル　:</p>
                    <p id="dairyLog_main_titleContent">{{ $last_title }}</p>
                </div>
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">内容　　　:</p>
                    <p id="dairyLog_main_contentContent">{!! $last_newContent !!}</p>
                </div>
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">画像　　　:</p>
                    <img id="dairyLog_main_imageContent" src="{{ $last_imageText }}"/>
                </div>
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">位置情報　:</p>
                    <p id="dairyLog_main_locationContent" 
                       data-lat="{{ $lastDairy['latitude'] ?? '' }}"
                       data-lng="{{ $lastDairy['longitude'] ?? '' }}">
                       {{ $last_locationText }}
                    </p>
                </div>
                <div class="dairyLog_main_div">
                    <p class="dairyLog_main_navi">場所　　　:</p> 
                    <p id="dairyLog_main_placeContent">{{ $last_place }}</p> 
                </div>
                <div id="mapid" style="height: 300px; width: 100%; margin-top: 15px;"></div>
            @else
                <p>現在、公開されている日記の投稿はありません。</p>
            @endif
        </div>
    </div>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
    
    {{-- 追加: LeafletのCSS/JS読み込み --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        let map = null; 
        let marker = null; 
        
        // Leafletの初期化とマーカー設置の統合関数
        function renderMap(lat, lng, centerMap = true) {
            const mapContainer = document.getElementById('mapid');
            const parsedLat = parseFloat(lat);
            const parsedLng = parseFloat(lng);

            if (isNaN(parsedLat) || isNaN(parsedLng)) {
                // 位置情報がない場合は地図を非表示にして終了
                if (mapContainer) mapContainer.style.display = 'none';
                return;
            }

            // 位置情報があれば表示を確定
            if (mapContainer) mapContainer.style.display = 'block';

            if (!map) {
                // マップが存在しない場合、新しいマップを作成
                map = L.map('mapid').setView([parsedLat, parsedLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
            }

            // 既存のマーカーを削除
            if (marker) {
                map.removeLayer(marker);
            }
            
            // 新しいマーカーを追加
            marker = L.marker([parsedLat, parsedLng]).addTo(map);
            
            // 中心を移動
            if (centerMap) {
                map.setView([parsedLat, parsedLng], 13);
            }

            // 💡 描画バグ対策: 描画サイズを強制的に再計算
            // setTimeout で遅延させることで、DOMが確定してから Leafletに命令が届くようにする
            setTimeout(() => {
                map.invalidateSize(true);
            }, 50); 
        }

        // ページロード時に実行される初期化関数
        window.onload = function() {
            const locationP = document.getElementById('dairyLog_main_locationContent');
            const initialLat = locationP ? locationP.getAttribute('data-lat') : null;
            const initialLng = locationP ? locationP.getAttribute('data-lng') : null;
            
            // マップの初期表示
            renderMap(initialLat, initialLng);
        };

        // ボタンクリック時に呼び出される関数
        function updateMap(lat, lng) {
            // 文字列の 'null' や 'undefined' 対策
            const isNull = lat === 'null' || lng === 'null';
            if (isNull) {
                renderMap(null, null); // 位置情報なしとして描画（非表示）
            } else {
                renderMap(lat, lng);
            }
        }
    </script>
@endsection