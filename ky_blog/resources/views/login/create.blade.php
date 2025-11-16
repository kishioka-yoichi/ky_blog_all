@section('title')
    OMOIDay 
@endsection

@extends('layouts.loginapp')

@section('content')
<div id="login_frame">
    <form action="/create" method="post">
        <h3>アカウント作成</h3>
        @csrf
        <div id="login_frame_main">
            <div id="login_frame_input">
                <div><tr><th>id: </th><td><input type="text" name="id"></td></tr></div>
                <div><tr><th>pass: </th><td><input type="password" name="pass"></td></tr></div>
                <div><tr><th>名前: </th><td><input type="text" name="name"></td></tr></div>
            </div>
            <tr><td><input class="login_button" type="submit" name="send" value="作成"></td></tr>
        </div>
    </form>
</div>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
@endsection