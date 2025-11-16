<html>
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Antique&display=swap" rel="stylesheet">
    <style>
    body {background-color: #041f06; font-size:16pt; color:#999; margin: 30px; font-family: 'Zen Antique', serif;}
    h1 { font-size:70pt; text-align:center; color:#ebebeb; padding: 30px 0px 30px 0px;
        margin:-20px 0px -30px 0px; letter-spacing:-4pt; }
    h3 { color: #2d0800; text-align: center; }
    input{ width: 300px; height: 30px;}
    .menutitle {font-size:14pt; font-weight:bold; margin: 0px; }
    .content {margin:10px; }
    #login_frame {background-color: #ebebeb; display:flex; flex-flow: column; width: fit-content; padding: 80px; margin-left: auto; margin-right: auto;}
    #login_frame_main { text-align: center; }
    #login_frame_input { font-size: 15pt; color: #2d0800; margin-bottom: 30px;}
    .login_button { width: 100px; background-color: #63a500; color: #2d0800;}
    #linkLogin {position:relative; top: 60px;}
    #linkLogin div{margin-left: auto; margin-right: auto;}
    a {color: #63a500; font-size: 15px;}
    .footer { text-align:right; font-size:10pt; margin:10px;
    border-top:solid 0.5px #d3d3d3; color:#ebebeb; }
    </style>
</head>
<body>
    <h1>OMOIDay</h1>
    <div class="content">
        @yield('content')
    </div>
    <div class="footer">
        @yield('footer')
    </div>
</body>
</html>
