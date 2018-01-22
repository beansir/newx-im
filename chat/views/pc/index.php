<?php
/**
 * @var string $token 登录token
 * @var string $nickname 用户昵称
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;">
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <meta name="wap-font-scale" content="no">
    <title>NewX IM</title>
    <link href="/public/css/home.css" type="text/css" rel="stylesheet" charset="utf-8" />
</head>
<body>
<div class="container" id="app">
    <div class="nav">
        <div class="user_info">
            <img src="/public/images/user/avatar/default.jpg">
            <span v-text="nickname"></span>
        </div>
        <div class="nav_chat_list active">
            <img src="/public/images/group.png">
            <span>NewX IM</span>
        </div>
    </div>

    <div class="content" id="container">
        <div class="content_title">NewX Chat</div>

        <div class="content_chat" id="content_chat">
            <div class="content_chat_row" v-for="item in log">
                <div class="content_chat_row_time" v-text="item.date" v-if="item.date !== ''"></div>
                <div class="content_chat_row_data" v-if="item.content !== ''">
                    <div :class="'content_chat_row_data_avatar' + (token == item.token ? ' right' : ' left')">
                        <img :src="item.avatar">
                    </div>
                    <div :class="'content_chat_row_data_info' + (token == item.token ? ' right' : ' left')">
                        <div :class="'content_chat_row_data_info_name' + (token == item.token ? ' right' : ' left')"
                             v-text="item.nickname">
                        </div>
                        <div :class="'content_chat_row_data_info_con' + (token == item.token ? ' right self' : ' left friend')"
                             style="text-align: center"
                             v-text="item.content">
                        </div>
                    </div>
                </div>
                <div class="content_chat_msg" v-text="item.message" v-if="item.message !== ''"></div>
            </div>
        </div>

        <div class="content_submit">
            <textarea class="content_submit_text" v-model="content" v-on:keyup.enter="submit"></textarea>
            <div class="content_submit_do">
                <button @click="submit">发送</button>
            </div>
        </div>
    </div>
</div>
<script src="/public/js/jquery.min.js"></script>
<script src="/public/js/vue.js"></script>
<script>
    new Vue({
        el: '#app',
        data: {
            token: '<?= $token ?>',
            nickname: '<?= $nickname ?>',
            log: [],
            socket: '',
            content: ''
        },
        methods: {
            // 连接WebSocket
            webSocket: function () {
                var _self = this;
                this.socket = new WebSocket('ws://47.96.169.56:9502?token=' + this.token);
                // 连接成功
                this.socket.onopen = function (evt) {
                    console.log("connected");
                };
                // 连接断开
                this.socket.onclose = function (evt) {
                    console.log("closed");
                };
                // 连接错误
                this.socket.onerror = function (evt, e) {
                    console.log('connect fail');
                };
                // 服务端数据
                this.socket.onmessage = function (evt) {
                    var res = JSON.parse(evt.data);
                    switch (res.code) {
                        case 1: // 正常登录
                            _self.log.push(res.data);
                            break;
                        case 2: // 被迫下线
                            alert('您的账号在其他地方登录，请重新登录');
                            location.href = '/user/login';
                            break;
                    }
                };
            },
            submit: function () {
                var param = {
                    token: this.token,
                    content: this.content
                };
                this.socket.send(JSON.stringify(param));
                this.content = '';
            }
        },
        mounted: function() {
            this.webSocket();
        },
        watch: {
            log: function () {
                this.$nextTick(function () {
                    var div = document.getElementById('content_chat');
                    div.scrollTop = div.scrollHeight;
                });
            }
        }
    });
</script>
</body>
</html>