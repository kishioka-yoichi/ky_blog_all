<?php

namespace App\Http\Controllers;

use App\Models\Account; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class LoginController extends Controller {

    public function loginGet(Request $request) {
        $warning = "";
        return view('login.login')->with('warning',$warning);
    }
    public function loginPost(Request $request) {
        $inputID = $request->id;
        $inputPass = $request->pass;

        // 1. Eloquentを使ってIDに一致するアカウントを検索
        // プライマリキー（id）が文字列なので、Account::find($inputID)で取得できる
        $account = Account::find($inputID);

        // 2. アカウントが存在しない、またはパスワードが一致しないかチェック
        if (is_null($account)) {
            // IDに一致するアカウントがない場合
            $warning = "IDまたはパスワードが間違っている可能性";
            return redirect('/')->with('warning', $warning);
        } else if ($account->pass === $inputPass) {
            // パスワード比較でログイン処理
            session()->put('userID', $inputID); // ログイン中ユーザのIDをセッションに保存
            return redirect('/main');
        } else {
            // パスワードが一致しない場合
            $warning = "IDまたはパスワードが間違っている可能性";
            return redirect('/')->with('warning', $warning);
        }
    }

    public function add(Request $request) {
        return view('login.index');
    }

    public function mailSend(Request $request) {
        $mail = $request->mail;
        session()->put('provisionalMail', $mail); // 一時的にメールをセッションに保存しておく
        $code = str_pad(random_int(0,9999),4,0, STR_PAD_LEFT);
        session()->put('mailPassCode', $code); // パスコードも
        $subject = "OMOIDay仮会員登録のご案内";
        $message = "OMOIDayからメール送信しました。\r\nサイトに戻り以下のパスコードを入力をし、会員登録を完了してください。\r\nパスコード = " . $code;
        $headers = "From: youichipanda@gmail.com";
        mb_send_mail($mail, $subject, $message, $headers);
        return redirect('/code');
    }

    public function inputCode(Request $request) {
        return view('login.code');
    }

    public function judgeCode(Request $request) {
        $inputCode = $request->code;
        $code = session()->get('mailPassCode');
        if ($inputCode === $code) {
            return redirect('/create');
        } else {
            return redirect('/code');
        }
    }

    public function inputAccount(Request $request) {
        return view('login.create');
    }

    public function create(Request $request) {
        // リクエストから直接必要なデータを取得
        $param = [
            'id' => $request->id,
            'pass' => $request->pass,
            'mail' => session()->get('provisionalMail'),
            'name' => $request->name,
            'ip' => $_SERVER['REMOTE_ADDR'],
        ];

        // Eloquentのcreateメソッドを使って、accountsテーブルにデータを挿入
        $account = Account::create($param);

        // ログイン中のユーザーIDをセッションに保存
        session()->put('userID', $request->id);

        // 挿入が成功したことをログに出力
        if ($account) {
            Log::channel('custom_log')->info("New account created for user ID: " . $request->id);
        } else {
            Log::channel('custom_log')->error("Failed to create new account.");
        }

        // createDairyTable(" " . "dairy_". $id);
        return redirect('/main');
    }

    public function kiyaku(Request $request) {
        return view('login.kiyaku');
    }

    public function kiyakuPost(Request $request) {
        return redirect('/kiyaku');
    }
    
}