function getUrlParam(name){
    //正则表达式过滤
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    //正则规则
    // console.log("reg==="+reg);
    //search：返回URL的查询部分
    // console.log("location.search==="+location.search);
    //substr（1）：从字符串第一个位置中提取一些字符
    // console.log("location.search.substr(1)==="+location.search.substr(1));
    // match（）：在字符串内检索与正则表达式匹配的指定值，返回一个数组给r
    // console.log("window.location.search.substr(1).match(reg)==="+window.location.search.substr(1).match(reg));
    var r = window.location.search.substr(1).match(reg);
    //获取r数组中下标为2的值；（下标从0开始），用decodeURI（）进行解码
    // console.log("decodeURI(r[2])==="+decodeURI(r[2]));
    // console.log("-----------------分隔符------------------------");
    if (r != null) return decodeURI(r[2]); return null;

}



jQuery(document).ready(function () {
    var username = getUrlParam('username');
    $.ajax({
        type: "POST",
        url: "php/get_main.php",
        data: {
            do: "get_info",
            username: username
        },
        dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('连接错误：' + textStatus + errorThrown);
        },   //如果请求出错，弹出错误提示
        success: function (data) {



            // 导航栏

            if(data['now_session'] === 'no_login'){
                // 未登录状态导航栏
                // 头像
                $("#navAvatar").append("<img style='width: 50px; height: 50px; border-radius: 50%' src='img/no_login.png'>");
                // controller
                $(".user-control").append("<div><a href='login.html'>登录</a></div>");

            }
            else{
                // 登录状态导航栏
                // console.log(data['now_session']);
                var avatarNavUrl = data['avatar-nav'][0];
                // console.log("导航栏头像类型" + jQuery.type(avatarNavUrl));

                // 导航栏头像
                if (avatarNavUrl.length == 0){
                    $("#navAvatar").append("<img style='width: 50px; height: 50px; border-radius: 50%' src='img/default.jpg'>");
                }else{
                    $("#navAvatar").append("<img style='width: 50px; height: 50px; border-radius: 50%' src='" + avatarNavUrl + "'>");
                }

                $(".user-control").append("<div><a href='index.html?username=" + data['now_session'] + "'>我的博客</a></div>" +
                    "<div><a href='following.html?username=" + data['now_session'] + "'  class=\"follow-fan\">关注/粉丝</a></div>" +
                    "<div><a href='edit-info.html?username=" + data['now_session'] + "'  class=\"edit-info\">修改资料</a></div>" +
                    "<div><a href='#' class='log-out'>退出</a></div>");
                $(".logo").children().attr("href", "home.html?username=" + data['now_session']);
                $(".login-center").before("<li><a class=\"write_blog\" href='write_blog.html?username=" + username + "'><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>  写博客</a></li>");
            }
        }
    });
    // 退出登录
    $(document).on('click', '.log-out', function () {
        // alert("aa");
        $.ajax({
            type: 'post',
            url: 'php/login_out.php',
            dataType: 'json',
            error : function (XMLHttpRequest, textStatus, errorThrown) {
                alert('连接错误：' + textStatus + errorThrown);
            },
            success: function () {
                alert('退出成功！');
                window.location.href = 'login.html';
            }
        })
    });

    // 搜索
    $(document).on('click', '#search_button', function () {
        // alert('search');
        var key = $('.input-search').val();
        // alert(key);
        window.location.href = 'search_article.html?key=' + key;
    });
});