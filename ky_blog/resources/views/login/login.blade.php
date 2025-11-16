@section('title')
    OMOIDay 
@endsection

@extends('layouts.loginapp')

@section('content')
<div id="login_frame">
    <form action="/" method="post">
        <h3>ログイン</h3>
        @csrf
        <div id="login_frame_main">
            <div id="login_frame_input">
                <div><tr><th>id: </th><td><input type="text" name="id"></td></tr></div>
                <div><tr><th>pass: </th><td><input type="password" name="pass"></td></tr></div>
            </div>
            <?php Log::error('ログ出力の例パスは' . 'storage/logs/laravel.log');?>
            <div>{{ session('warning') }}</div>
            <tr><td><input class="login_button" type="submit" name="send" value="ログイン"></td></tr>
            <div id="linkLogin">
                <div><a href="https://ky-blog.com/add" style="text-decoration:none;">アカウント作成</a></div>
                <div><a href="https://ky-blog.com/kiyaku" style="text-decoration:none;">利用規約</a></div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
@endsection