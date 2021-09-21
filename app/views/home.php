<?php
 $HTTP_HOST = $_SERVER['HTTP_HOST'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        ul, li {
            list-style: none;
        }

        a {
           display: inline-block;
           width:300px;
           background-color: #919191;
           text-decoration: none;
           padding: 10px 30px;
           color: white;
           border-bottom: 1px solid;
           border-radius: 5px;
           
        }
    </style>
</head>
<body>
    <h1>Call API</h1>
    <ul></ul>
        <li><a href = "http://<?=$HTTP_HOST?>/api/user"> GET: <?=$HTTP_HOST?>/api/user</a></li>
        <li><a href = "http://<?=$HTTP_HOST?>/api/user/page/1"> GET: <?=$HTTP_HOST?>/api/user/page/1</a></li>
        <li><a href = "http://<?=$HTTP_HOST?>/api/user/62"> GET: <?=$HTTP_HOST?>/api/user/62</a></li>
        <li><a href="#"> POST:  <?= $HTTP_HOST ?>/api/user</a></li>
        <li><a href="#"> PUT:  <?= $HTTP_HOST ?>/api/user/:userid</a></li>
        <li><a href="#"> DELETE:  <?= $HTTP_HOST ?>/api/user/:userid</a></li>
    </ul>
</body>
</html>
