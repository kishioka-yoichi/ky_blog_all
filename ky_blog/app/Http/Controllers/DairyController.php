<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Diary;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTime;

class DairyController extends Controller{
    
    public function dairyCreate(Request $request) {
        $inputArray = array('title'=>"", 'content'=>"", 'other'=>"", 'inputNull'=>false, 'imageSizeOver'=>false);
        return view('dairy.dairyCreate', ['inputArray' => $inputArray]);
    }

    public function dairyCreatePost(Request $request) {
        // ... (既存の変数取得) ...
        $userID = session()->get('userID');
        $oneDayNumberPosts = session()->get('oneDayNumberPosts');
        $title = $request->title;
        $content = $request->content;
        $imageFile = $request->file('image');
    
        // 変更点: 新しいフォームデータを取得
        $mode = $request->input('location_mode', 'manual'); // デフォルトはmanual
        $manualPlace = $request->input('manual_place');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $isPublic = $request->input('is_public', '0'); 

        // 緯度・経度を float または null に変換
        $lat = is_numeric($latitude) ? (float)$latitude : null;
        $lng = is_numeric($longitude) ? (float)$longitude : null;

        $imgData = "";
        $imageSizeOver = false;

        if ($oneDayNumberPosts < 5) {
            if (isset($content) && isset($title)){
            
                // 1. 画像ファイルがアップロードされた場合
                if ($imageFile) {
                    $fileSize = $imageFile->getSize();
                
                    if ($fileSize > 2097152) { // 2MBの制限（16MBから2MBに修正）
                        $imageSizeOver = true;
                    } else {
                        // バイナリデータを取得し、すぐに Base64 エンコードして $imgData に格納
                        $binaryData = file_get_contents($imageFile->getRealPath());
                        $imgData = base64_encode($binaryData); 
                    }
                } 
            
                // 2. 画像サイズがオーバーしていない場合、または画像がそもそも添付されていない場合
                if (!$imageSizeOver) {
                    //  画像の有無に関わらず、Base64エンコード済みの $imgData (空文字列またはデータ) を渡す
                    $this->insertDiary($userID, $title, $content, $imgData, $mode, $manualPlace, $lat, $lng, $isPublic);
                    return redirect('/dairyLog');
                } else {
                     // ... (画像サイズエラー処理: エラー時の inputNull=true は不自然なので修正)
                     $inputArray = array('title'=>$title, 'content'=>$content, 'other'=>"画像サイズが大きすぎます。", 'inputNull'=>false, 'imageSizeOver'=>true);
                     return view('dairy.dairyCreate', ['inputArray' => $inputArray]);
                }
            } else {
                // ... (入力NULLエラー処理)
            }
        } else {
            // ... (投稿数上限エラー処理)
        }
    
        // エラー時のフォールバックビュー (投稿数制限またはその他の致命的なエラー)
        $inputArray = array('title'=>$title, 'content'=>$content, 'other'=>"投稿数上限または何らかのエラーが発生しました。", 'inputNull'=>true, 'imageSizeOver'=>false);
        return view('dairy.dairyCreate', ['inputArray' => $inputArray]);
    }

    function insertDiary(string $userID, string $title, string $content, string $img_data, 
                     string $mode,
                     ?string $manualPlace = null,
                     ?float $lat = null, 
                     ?float $lng = null,
                     string $isPublic = '0') 
    {
        $placeToSave = null;
        $latitudeToSave = null;
        $longitudeToSave = null;

        if ($mode === 'manual') {
            // --- モード 1: 手動入力 ---
            $placeToSave = $manualPlace ?? '場所なし(手動)';
            $latitudeToSave = null; 
            $longitudeToSave = null;
        
        } elseif ($mode === 'auto' && $lat !== null && $lng !== null) {
            // --- モード 2: 自動取得 (既存の Nominatim ロジック) ---
            $latitudeToSave = $lat;
            $longitudeToSave = $lng;

            // ... (Nominatim API呼び出しと場所の並べ替えロジックはそのまま) ...

            $response = Http::withHeaders([
                'User-Agent' => 'ky-blog/1.0 (youichipanda@gmail.com)'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 18,
                'addressdetails' => 1
            ]);
        
            // ... (API成功/失敗時の場所設定ロジックをそのまま使用) ...
            if ($response->successful()) {
                $data = $response->json();
                $address = $data['address'] ?? [];
            
                // 日本の住所表記順に並べ替えるロジック (省略せず記述してください)
                $parts = [];
                if (($address['country_code'] ?? null) === 'jp') { $parts[] = '日本'; }
                if (!empty($address['postcode'])) { $parts[] = $address['postcode']; }
                if (!empty($address['state'])) { $parts[] = $address['state']; }
                $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? null;
                if (!empty($city)) { $parts[] = $city; }
                $suburb = $address['suburb'] ?? $address['neighbourhood'] ?? $address['road'] ?? $address['house_number'] ?? null;
                if (!empty($suburb)) { $parts[] = $suburb; }
            
                if (!empty($parts)) {
                    $placeToSave = implode(' ', $parts); 
                } else {
                    $placeToSave = $data['display_name'] ?? '場所特定失敗';
                }
            
            } else {
                $status = $response->status();
                Log::error('Nominatim API Request Failed. Status: ' . $status . ', Lat: ' . $lat . ', Lng: ' . $lng);
                $placeToSave = '場所APIエラー: ' . $status;
            }
        } else {
            // 'auto' モードだが位置情報が取得できなかった、または lat/lng が無効な値の場合
            $placeToSave = '場所なし(自動取得失敗)';
            $latitudeToSave = null; 
            $longitudeToSave = null;
        }
    
        // DBへの保存
        $insetValues = "userID: {$userID}, title: {$title}, content: {$content}, img_data: {$img_data}, lat: {$latitudeToSave}, lng: {$longitudeToSave}, place: {$placeToSave}";

        $diary = Diary::create([
            'user_id' => $userID,
            'title' => $title,
            'content' => $content,
            'image' => $img_data,
            'latitude' => $latitudeToSave, // 修正後の値を保存
            'longitude' => $longitudeToSave, // 修正後の値を保存
            'place' => $placeToSave, // 修正後の値を保存
            'is_public' => (bool)($isPublic === '1'), // 公開フラグを保存 (boolean型にキャスト)
        ]);

        if ($diary) {
            Log::channel('custom_log')->info('insertDiary: Successed to save diary post' . $insetValues);
        } else {
            Log::channel('custom_log')->info('insertDiary: Failed to save diary post' . $insetValues);
        }
    }

    public function getDiaries(string $userID) {
        // 引数として渡された userID と一致する user_id を持つ日記投稿を取得する
        $diaries = Diary::where('user_id', $userID)->get();

        // 取得した件数をログに出力
        Log::channel('custom_log')->info("userID='{$userID}', Number of diary posts retrieved: " . $diaries->count());
        
        // 修正点: Collection を PHPのネイティブ配列に変換して返す
        return $diaries->toArray(); 
    }

    public function dairyLog(Request $request) {
        $userID = session()->get('userID');
        $dairys = $this->getDiaries($userID);
        $this->setOneDayNumberPosts($dairys);
        return view('dairy.dairyLog', ['dairys' => $dairys]);
    }
    
    public function dairyLogPost(Request $request) {
        $items = DB::select('select * from accounts');
        return redirect('/add');
    }

    function setOneDayNumberPosts(array $dairys) {
        date_default_timezone_set('Asia/Tokyo'); //日本のタイムゾーンに設定
        $numberPosts = 0;
        $todayTime = new DateTime();
        $today = $todayTime->format('Y-m-d');
        foreach($dairys as $dairy){
            $createdDayTime = new DateTime($dairy["created_at"]);
            $createdDay = $createdDayTime->format('Y-m-d');
            Log::error("AAA created_at" . $createdDay . "AAA today" . $today);
            // Log::error("CCC $dairy[created_at]" );
            if ($today === $createdDay) { // 今日ならカウント
                $numberPosts += 1;
            }
        }
        session()->put('oneDayNumberPosts', $numberPosts);
        Log::error("ZZZ numberPosts" . $numberPosts );
    }

    /**
     * 全ユーザーの公開設定された日記投稿を取得する
     */
    public function getPublicDiaries() {
        $diaries = Diary::where('is_public', true)
                         // リレーション名が 'account' ではない場合は修正
                         ->with('account') 
                         ->orderBy('created_at', 'desc')
                         ->get();
    
        $dairysWithUserName = $diaries->map(function ($diary) {
            $array = $diary->toArray(); // まず元の日記データを配列化

            // 投稿者情報がリレーションで取得できているか確認
            if ($diary->account) {
                // 重要: 'name' の部分を Accounts テーブルの実際のユーザー名カラム名に修正してください。
                $userName = $diary->account->name ?? '不明なユーザー (Nameカラムなし)';
            } else {
                // リレーションが失敗した場合 (user_id が Accounts テーブルに存在しないなど)
                $userName = 'アカウント情報欠落 (ID: ' . $diary->user_id . ')';
            }
        
            // 修正: 'user_name' キーを確実に配列に追加
            $array['user_name'] = $userName;
            return $array;
        });

        return $dairysWithUserName->toArray(); 
    }

    /**
     * みんなの投稿画面を表示する
     */
    public function publicDiaryLog() {
        $dairys = $this->getPublicDiaries();
    
        // setOneDayNumberPosts は不要
    
        return view('dairy.publicDiaryLog', ['dairys' => $dairys]);
    }
}