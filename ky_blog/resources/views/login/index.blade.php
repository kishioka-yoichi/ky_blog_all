@section('title')
    OMOIDay 
@endsection

@extends('layouts.loginapp')

@section('content')
<div id="login_frame">
    <form action="/add" method="post">
        <h3>確認メール</h3>
        @csrf
        <div id="login_frame_main">
            <div id="login_frame_input">
                <div><tr><th>mail: </th><td><input type="text" name="mail"></td></tr></div>
            </div>
            <tr><td><input class="login_button" type="submit" name="send" value="送信"></td></tr>
        </div>
    </form>
</div>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
@endsection