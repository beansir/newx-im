<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1,minimum-scale=1,user-scalable=no,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <meta name="wap-font-scale" content="no">
    <title>chat</title>
    <link href="/public/css/element-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        #app {
            font-family: "Avenir", Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-align: center;
            color: #2c3e50;
            height: 100%;
        }
        * {
            padding: 0;
            margin: 0;
        }
        html {
            height: 100%;
        }
        body {
            height: 100%;
            text-align: center;
        }
        .el-button--primary,
        .el-button--primary.is-active,
        .el-button--primary:focus
        {
            background-color: #58C4AD;
            border-color: #58C4AD;
        }
        .el-button--primary:hover {
            background-color: #74DEC6;
            border-color: #74DEC6;
        }
        .content {
            width: 100%;
            height: 100%;
        }
        .top {
            width: 100%;
            background-color: #58C4AD;
        }
        .login-con {
            width: 450px;
            height: 300px;
            position: fixed;
            left: 50%;
            top: 50%;
            margin: -150px 0 0 -225px;
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.3);
        }
        .login-title {
            font-family: 'Microsoft YaHei';
            color: #333333;
            letter-spacing: 2px;
            width: 100%;
            height: 65px;
            line-height: 65px;
            font-size: 18px;
        }
        .login-submit {
            float: left;
            width: 100%;
            margin-top: 10px;
        }
        .login-desc {
            width: 100%;
            float: left;
            color: silver;
            font-size: 12px;
            letter-spacing: 2px;
            margin-top: 30px;
        }
        .el-input {
            margin: 10px 0;
        }
        input:-webkit-autofill{
            -webkit-box-shadow: 0 0 0 400px #fff inset;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="content">
        <div class="top" :style="bgStyle"></div>
    </div>
    <div class="login-con">
        <div class="login-title">User Login</div>
        <el-input placeholder="name" v-model="name"></el-input>
        <el-input type="password" placeholder="password" v-model="pwd"></el-input>
        <el-button type="primary" class="login-submit" @click="submit">GO</el-button>
        <h5 class="login-desc">Newx Chat System</h5>
    </div>
</div>
<script src="/public/js/jquery.min.js"></script>
<script src="/public/js/vue.js"></script>
<script src="/public/js/element-ui.js"></script>
<script>
    new Vue({
        el: '#app',
        data: {
            bgStyle: '',
            name: '',
            pwd: ''
        },
        methods: {
            submit: function () {
                if (this.name == '') {
                    this.error('请填写用户名');
                    return;
                }
                if (this.pwd == '') {
                    this.error('请填写密码');
                    return;
                }
                var self = this;
                $.post('/api/user/login', {name: this.name, password: this.pwd}, function (res) {
                    if (res.status) {
                        self.success(res.msg);
                        setTimeout(function () {
                            location.href = '/';
                        }, 1000);
                    } else {
                        self.error(res.msg);
                    }
                }, 'json');
            },
            success: function (msg) {
                this.$message({
                    message: msg,
                    type: 'success'
                });
            },
            error: function (msg) {
                this.$message({
                    message: msg,
                    type: 'error'
                });
            }
        },
        mounted: function() {
            var clientHeight = document.body.clientHeight;
            this.bgStyle = 'height: ' + clientHeight / 2 + 'px';
        }
    });
</script>
</body>
</html>