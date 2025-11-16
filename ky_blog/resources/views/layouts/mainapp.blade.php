<html>
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Antique&display=swap" rel="stylesheet">
    <title>@yield('title')</title>
    <style>
    body {background-color: #041f06; font-size:16pt; color:#999;  font-family: 'Zen Antique', serif;}
    #menu_tab {border-bottom: solid 0.2px #778975;}
    #main_link {font-size:40pt; color:#ebebeb; padding-right: 100px;}
    .menu_item {font-size:20pt; color:#ebebeb;}
    #dairyLog_content{display: flex;}
    #dairyLog_tab{border-right:solid 0.2px #778975;}
    .dairyLog_tab_button{background-color: transparent; color:#ebebeb;}

    #dairyLog_main {background-color: #ebebeb; display:flex; flex-flow: column; width: 50vw; height: 100vh;
         padding: 1vw; margin-left: auto; margin-right: auto; color: #2d0800;}
    .dairyLog_main_div{display: flex;align-items: center;}
    .dairyLog_main_navi{font-size: 10pt; min-width: 80px; flex-shrink: 0;}
    #dairyLog_main_titleContent{font-size: 30pt;}
    #dairyLog_main_createdAtContent{font-size: 10pt;}
    #dairyLog_main_contentContent{font-size: 15pt;}
    #dairyLog_main_imageContent{width: 300px; height: 300px;}
    #dairyLog_main_userIDContent{font-size: 10pt; color: #2d0800; white-space: nowrap; overflow: hidden; }

    #dairyCreate_frame {background-color: #ebebeb; display:flex; flex-flow: column; width: fit-content; padding: 80px; margin-left: auto; margin-right: auto;}
    #dairyCreate_main { text-align: left; }
    #dairyCreate_input { font-size: 15pt; color: #2d0800; margin-bottom: 30px;}
    .insert_button { width: 100px; background-color: #63a500; color: #2d0800;}
    .footer { text-align:right; font-size:10pt; margin:10px;
    border-top:solid 0.2px #778975; color:#ebebeb; }
    </style>
</head>
<body>
    <div id="menu_tab">
        <a href="https://ky-blog.com" style="text-decoration:none;" id="main_link">OMOIDay</a>
        <a href="https://ky-blog.com/dairyLog" style="text-decoration:none;" class="menu_item">日記履歴</a>
        <a href="https://ky-blog.com/dairyCreate" style="text-decoration:none;" class="menu_item">日記作成</a>
        <a href="https://ky-blog.com/publicDiaries" style="text-decoration:none;" class="menu_item">みんなの投稿</a>
    </div>

    <div class="content">
        @yield('content')
    </div>
    <div class="footer">
        @yield('footer')
    </div>
</body>
</html>