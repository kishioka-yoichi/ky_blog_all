@section('title')
    OMOIDay 
@endsection

@extends('layouts.loginapp')

@section('content')
<div id="login_frame">
    <form action="/code" method="post">
        <h3>確認メール</h3>
        @csrf
        <div id="login_frame_main">
            <div id="login_frame_input">
                <div><tr><th>メールに記載したパスコードを入力してください（４桁数値）:</th><td><input type="number" name="code"></td></tr></div>
            </div>
            <tr><td><input class="login_button" type="submit" name="send" value="確定"></td></tr>
        </div>
    </form>
</div>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
@endsection