<?php
/**
 * 异常视图
 * @var \newx\exception\BaseException $exception
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1,minimum-scale=1,user-scalable=no,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <meta name="wap-font-scale" content="no">
    <title>NewX Exception</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 14px;
            height: 100%;
        }
        .ui_content {
            width: 60%;
            margin: 50px auto 0;
            background: #EEEEEE;
            border-radius: 10px;
        }
        .ui_content_title {
            width: 100%;
            height: 60px;
            line-height: 60px;
            border-bottom: 1px solid #fff;
            text-align: center;
            color: #008017;
            letter-spacing: 1px;
        }
        .ui_content_name {
            width: 100%;
            height: 40px;
            line-height: 40px;
            border-bottom: 1px solid #fff;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
        }
        .ui_content_data {
            width: 100%;
            padding: 20px;
        }
        .ui_content_data p {
            font-size: 16px;
            font-weight: bold;
        }
        .ui_content_data_row {
            width: 100%;
            height: 35px;
            line-height: 35px;
        }
    </style>
</head>
<body>
<div class="ui_content">
    <div class="ui_content_title"><h2>NewX Exception Error</h2></div>
    <div class="ui_content_name"><?= $exception->name ?></div>
    <div class="ui_content_data">
        <p><?= $exception->getMessage() ?></p>
        <div class="ui_content_data_row">
            - <?= $exception->getFile() ?> : <?= $exception->getLine() ?>
        </div>
        <?php $traces = $exception->getTrace(); ?>
        <?php if (!empty($traces)) { ?>
            <?php foreach ($traces as $trace) { ?>
                <?php if (array_key_exists('file', $trace) && array_key_exists('line', $trace)) { ?>
                    <div class="ui_content_data_row">
                        - <?= $trace['file'] ?> : <?= $trace['line'] ?>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
</div>
</body>
</html>