@extends('layouts.loginapp')

@section('content')
    <table>
        <tr><th>id</th><th>pass</th><th>mail</th><th>name</th><th>ip</th></tr>
        @foreach ($items as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->pass}}</td>
                <td>{{$item->mail}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->ip}}</td>
            </tr>
        @endforeach
    </table>
    <form action="/add" method="post">
    <table>
        @csrf
        <tr><th>id: </th><td><input type="text" name="id"></td></tr>
        <tr><th>pass: </th><td><input type="text" name="pass"></td></tr>
        <tr><th>mail: </th><td><input type="text" name="mail"></td></tr>
        <tr><th>name: </th><td><input type="text" name="name"></td></tr>
        <tr><th>ip: </th><td><input type="text" name="ip"></td></tr>
        <tr><td><input type="submit" name="send"></td></tr>
    </table>
    </form>
@endsection

@section('footer')
    ©2023 Yoichi Kishioka 
@endsection