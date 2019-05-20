<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"/project/recycle/public/../app/index/view/login/index.html";i:1556082789;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登录</title>
    <link rel="stylesheet" href="/static/common/css/ssbase.css">
    <link rel="stylesheet" href="/static/common/css/animate.min.css">
    <link rel="stylesheet" href="/static/common/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/common/css/font-awesome.min.css">
    <link rel="stylesheet" href="/static/index/css/style.css">
    <style>
        body, html {
            background: #f5f5f5
        }

        .input-group {
            margin-top: 20px
        }

        .send_parent {
            padding: 0;
        }

        .send_parent > .send_info {
            padding: 6px 12px;
            border: none;
            background: #eee
        }
    </style>
</head>
<body>
<div class="con_body">
    <div class="flex-box flex-b flex-col-c ">
        <img src="/static/index/images/trasher.png" width="20%" alt="">
    </div>
    <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-phone" aria-hidden="true"></i>
            </span>
        <input type="text" class="form-control" name="tel" maxlength="11" onchange="tels($(this))" placeholder="请输入手机号">
    </div>
    <div class="input-group tab2">
            <span class="input-group-addon">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
        <input type="password" name="password1" class="form-control" placeholder="请输入密码">
    </div>

    <div class="input-group tab1 hide">
        <input type="text" class="form-control" name="yzm" onchange="checkSms()" placeholder="验证码">
        <span class="input-group-addon send_parent">
    <button type="button" class="send_info" onclick="printyzm()">发送短信</button>
    </span>
    </div>

    <div class="input-group login_btn" login-type="pwd">
        登&nbsp;&nbsp;&nbsp;&nbsp;录
    </div>
    <div class="forgot flex-box flex-b input-group">
        <span>&nbsp;</span>
        <button class="tab_to_to" type="button" style="background: #fff;border: none;font-size: 16px;">切换登录</button>
    </div>
</div>
</body>
<script src="/static/common/js/jquery-2.1.4.min.js"></script>
<script src="/static/index/js/jquery.md5.js"></script>
<!-- v-3.1.1 -->
<script src="/static/common/layer/layer.js"></script>
<script>
    $(function () {
        var num = 1;
        $(".tab_to_to").click(function () {
            num++;
            if (num % 2 == 0) {
                $(".tab2").addClass("hide");
                $(".tab1").removeClass("hide");
                $(".login_btn").attr('login-type', 'sms');
            } else {
                $(".tab2").removeClass("hide");
                $(".tab1").addClass("hide");
                $(".login_btn").attr('login-type', 'pwd');
            }
        });
        $(".login_btn").click(function () {
            var tel = $("input[name='tel']");
            var pwd = $("input[name='password1']").val();
            var telnumber = tel.val(), yzm = $("input[name='yzm']").val();
            tels(tel);
//            printyzm();
            var login_type = $(".login_btn").attr('login-type');
            if (telnumber != '' && (yzm != '' || pwd != '')) {
//                window.location.href = '/index/index/index.html';
                var yzmCookie = sessionStorage.getItem("yzm");
//                alert(yzmCookie);
                if (login_type == 'pwd') {
                    var md_pwd = $.md5(pwd);
                    $.ajax({
                        url: "/index/Login/doLogin",
                        type: "POST",
                        data: {"login_type": login_type, "tel": telnumber, "psw": md_pwd},
                        success: function (res) {
                            res = JSON.parse(res);
//                            //提示
                            if (res.code != 1) {
                                layer.open({
                                    content: res.msg
                                    , skin: 'msg'
                                    , time: 2 //2秒后自动关闭
                                });
                                $("input[name='password1']").val('');
                            } else {
                                window.location.href = res.url;
                            }
                        }
                    })
                } else if (login_type == 'sms') {
//                    alert(yzm);
//                    alert(yzmCookie);

                    if (yzmCookie != yzm) {
                        //提示
                        layer.open({
                            content: '验证码有误！'
                            , skin: 'msg'
                            , time: 1 //2秒后自动关闭
                        });
                    } else {
                        $.ajax({
                            url: "/index/Login/doLogin",
                            type: "POST",
                            data: {"login_type": login_type, "tel": telnumber},
                            success: function (res) {
                                res = JSON.parse(res);
//                            //提示
                                if (res.code != 1) {
                                    layer.open({
                                        content: res.msg
                                        , skin: 'msg'
                                        , time: 2 //2秒后自动关闭
                                    });
                                } else {
                                    window.location.href = res.url;
                                }
                            }
                        })
                    }
                }
            }
        })
    });
    // 手机号码
    var mobile = /^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/;

    function tels(obj) {
        var tel = obj.val();
        if (tel == '') {
            layer.open({
                content: '手机号不能为空！'
                , btn: '确定'
            });
            obj.val("");
            return false;
        } else if (!mobile.test(tel)) {
            layer.open({
                content: '手机号格式有误！'
                , btn: '确定'
            });
            obj.val("");
            return false;
        }
    }

    //
    //验证码倒计时
    var count = 60;

    $(".send_info").on("click", function () {
        var tel = $("input[name='tel']").val();

        $(this).prop("disabled", true).html("重新发送" + count + "s");
        clearInterval(timer);
        var that = $(this);
        var timer = setInterval(function () {
            if (count == 0) {
                clearInterval(timer);
                that.prop("disabled", false).html("重新发送");
                count = 60;
            } else {
                count--;
                that.prop("disabled", true).html("重新发送" + count + "s");
            }
        }, 1000)
    });

    // 验证码
    function printyzm() {
        var yzm = $("input[name='yzm']").val();
        var tel = $("input[name='tel']").val();
        var url = '/index/Login/smsValidate?yzm=' + yzm + '&tel=' + tel;
        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                // 验证码输入验证：不对则清空验证码输入框
                if (res) {
                    sessionStorage.setItem("yzm", res);
                } else {
                    //提示
                    layer.open({
                        content: '验证码有误！'
                        , skin: 'msg'
                        , time: 1 //2秒后自动关闭
                    });
                }

            }
        })
    }

    //    校验短信验证码
    function checkSms() {
        var yzm = $("input[name='yzm']").val();
        var yzmCookie = sessionStorage.getItem("yzm");
        if (yzm !== yzmCookie) {
            $("input[name='yzm']").val('');
        }
    }
</script>
</html>