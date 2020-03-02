(function ($) {
    $.getUrlParam = function (name) {

        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");

        var r = window.location.search.substr(1).match(reg);

        if (r != null) return unescape(r[2]); return null;
    }

})(jQuery);

function getUrlParam(name){
    //正则表达式过滤
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    //正则规则
    // console.log("reg==="+reg);
    //search：返回URL的查询部分
    // console.log("location.search==="+location.search);
    //substr（1）：从字符串第一个位置中提取一些字符
    // console.log("location.search.substr(1)==="+location.search.substr(1));
    //match（）：在字符串内检索与正则表达式匹配的指定值，返回一个数组给r
    // console.log("window.location.search.substr(1).match(reg)==="+window.location.search.substr(1).match(reg));
    var r = window.location.search.substr(1).match(reg);
    //获取r数组中下标为2的值；（下标从0开始），用decodeURI（）进行解码
    // console.log("decodeURI(r[2])==="+decodeURI(r[2]));
    // console.log("-----------------分隔符------------------------");
    if (r != null) return decodeURI(r[2]); return null;

}


jQuery(document).ready(function () {
    var username = getUrlParam('username');
    // var usernameNew = getUrlParam('username');
    // var elsePram = getUrlParam('articleid');
    // console.log('newusername: ', usernameNew);
    // console.log(elsePram);
    $.ajax({
        type: "post",
        url: "php/get_main.php",
        data: {
            do: "get_info",
            username: username
        },
        dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('连接错误：' + textStatus + errorThrown);
        },
        success: function (data) {
            // console.log(data);
            // aside内头像
            var avatarUrl = data['avatar'][0];
            if (avatarUrl === undefined || avatarUrl === "") {
                // 当获取头像值为空，给它默认头像
                $("#userAvatar").append("<img style='width: 50px; height: 50px; border-radius: 50%' src='img/default.jpg'>");
            }
            else {
                // 否则就是头像的url
                $("#userAvatar").append("<img style='width: 50px; height: 50px; border-radius: 50%' src='" + avatarUrl + "'>");
            }



            // 关注按钮
            if (data['is_follow'] === false && data['now_session'] !== username){
                $(".profile-intro").append("<div class=\"btn-div-follow\"><button class=\"btn-follow-aside\">关注</button> </div>");
            }
            else if (data['now_session'] !== username && data['is_follow'] === true) {
                $(".profile-intro").append("<div class=\"btn-div-follow\"><button class=\"btn-follow-aside\">已关注</button> </div>");
            }


            // 关注 / 取消关注功能
            $(document).on('click', '.btn-follow-aside', function () {
                if (data['now_session'] === 'no_login'){
                    alert('请先登录！');
                }
                else {
                    // 被关注人username
                    // alert($(this).innerHTML);
                    alert(username);
                    if ($(this).html() === '已关注'){
                        // 取消关注
                        $(this).text('关注');
                        $.ajax({
                            type: 'post',
                            url: 'php/following.php',
                            data: {
                                do: 'no_focus',
                                username: username
                            },
                            dataType: 'json',
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                alert('连接错误：' + textStatus + errorThrown);
                            },
                            success: function () {
                                alert('取消关注成功！');
                            }
                        })
                    }
                    else if ($(this).html() === '关注') {
                        // 关注
                        $(this).text('已关注');
                        $.ajax({
                            type: 'post',
                            url: 'php/following.php',
                            data: {
                                do: 'focus',
                                username: username
                            },
                            dataType: 'json',
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                alert('连接错误：' + textStatus + errorThrown);
                            },
                            success: function () {
                                alert('关注成功！');
                            }
                        })
                    }
                }

            });




            // 个人资料内用户名
            var asideUserName = document.getElementsByClassName('text-truncate')[0];
            asideUserName.innerHTML = username;
            //个人资料url
            $("#userAvatar").attr('href', 'user_info.html?username=' + username);
            $(".name").attr('href', 'user_info.html?username=' + username);

            // 获得文章数量、粉丝数量、访问量等等
            var dataInfo = document.getElementsByClassName('data-info')[0];
            var textCenter = dataInfo.childNodes;

            var articleCount = textCenter[1].lastElementChild.childNodes[1];
            articleCount.innerHTML = data['article_num'];

            var fanCount = textCenter[3].lastElementChild.childNodes[1];
            fanCount.innerHTML = data['fans'];

            var followCount = textCenter[5].lastElementChild.childNodes[1];
            followCount.innerHTML = data['follow'];

            var commentCount = textCenter[7].lastElementChild.childNodes[1];
            commentCount.innerHTML = data['comment_num'];

            var visitedCount = document.getElementsByClassName("grade-box")[0];
            visitedCount.innerHTML = "访问：" + data['visited_num'][0];

            // 个人分类
            for(var i = 0; i < data['cate'].length; i++){
                var insertedUl = $("#cateUl");
                insertedUl.append("<li><a href='category.html?username=" + username +"&tag=" + data['cate'][i]['tag'] + "'>" + data['cate'][i]['tag'] + "<span class='count'>" + data['cate'][i]['num'] +"篇</span>");
            }

            // 时间戳转成xxxx年x月形式
            function timestampToTime(timestamp) {
                var date = new Date(timestamp * 1000);  // 时间戳为10位需*1000，时间戳为13位的话不需乘1000
                Y = date.getFullYear() + '年';
                M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '月';
                return Y+M;
            }



            // 归档
            // console.log(data['create_time']);
            // console.log(data['create_time'].length);
            // console.log(data['create_time'][0]['create_time']);
            var dateSortNum = {};
            for(var i = 0; i < data['create_time'].length; i++){
                data['create_time'][i][0] = timestampToTime(data['create_time'][i][0]);
                if(dateSortNum[data['create_time'][i][0]] === undefined){
                    dateSortNum[data['create_time'][i][0]] = 1;
                }
                else{
                    dateSortNum[data['create_time'][i][0]] += 1;
                }
            }
            // console.log(dateSortNum);


            for(key in dateSortNum) {
                // console.log(key);
                var key1 = key.replace(/年/, '-');
                var key2 = key1.replace(/月/, '');
                // console.log(key2);
                $("#dateSortNum").append("<li><a href='date_cate.html?username=" + username + "&date=" + key2 + "'>" + key + "<span class='count'>" + dateSortNum[key] + "篇</span></a></li>");
            }

            // 热门文章 top 5
            // console.log(data['hot_articles']);
            // console.log(data['hot_articles'].length);
            if(data['hot_articles'].length >= 5){
                for (var i = 0; i < 5; i++){
                    $("#hotArticleUl").append("<li><a href='article.html?username=" + username + "&article_id=" + data['hot_articles'][i]['article_id'] + "'>" + data['hot_articles'][i][0] +
                        "</a><p class='read'>阅读量：" + data['hot_articles'][i][1] + "</p></li>");
                }
            }else {
                for(var i = 0; i < data['hot_articles'].length; i++){
                    $("#hotArticleUl").append("<li><a href='article.html?username=" + username + "&article_id=" + data['hot_articles'][i]['article_id'] + "'>" + data['hot_articles'][i][0] +
                        "</a><p class='read'>阅读量：" + data['hot_articles'][i][1] + "</p></li>");
                }
            }
        }
    })
});