<!doctype html>
<html ng-controller="BaseController" lang="zh" ng-app="xiaohu" user-id="{{session('user_id')}}">
<head>
    <meta charset="UTF-8">
    <title>晓乎</title>
    <link rel="stylesheet" href="/node_modules/normalize-css/normalize.css">
    <link rel="stylesheet" href="/css/base.css">
    <script src="/node_modules/jquery/dist/jquery.js"></script>
    <script src="/node_modules/angular/angular.js"></script>
    <script src="/node_modules/@uirouter/angularjs/release/angular-ui-router.js"></script>
    <script src="/js/base.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/question.js"></script>
    <script src="/js/user.js"></script>
    <script src="/js/answer.js"></script>
</head>
<body>
{{--<div ng-controller="ParentController">
    <div ng-controller="TestController">
        name是[: name :]
    </div>

    <div>
        name是 [: name :]
    </div>
</div>--}}
<div class="navbar clearfix">
    <div class="fl">
        <div ui-sref="home" class="navbar-item brand">晓乎</div>
        <form ng-submit="Question.go_add_question()" id="quick_ask" ng-controller="QuestionAddController">
            <div class="navbar-item">
                <input ng-model="Question.new_question.title" type="text" class="input" placeholder="说点什么">
            </div>
            <div class="navbar-item">
                <button type="submit">提问</button>
            </div>

        </form>
    </div>
    <div class="fr">
        <div  ui-sref="home" class="navbar-item">首页</div>
        @if(is_logged_in())
            <div class="navbar-item">{{session('username')}}</div>
            <a  href="{{url('/api/logout')}}" class="navbar-item">登出</a>
        @else
            <div  ui-sref="login" class="navbar-item">登陆</div>
            <div  ui-sref="signup" class="navbar-item">注册</div>
        @endif
    </div>
</div>
{{--渲染视图--}}
<div class="page">
    <div ui-view></div>
</div>

<script type="text/ng-template" id="comment.tpl">
   <div class="comment-block">
        <div class="hr"></div>
        <div class="comment-item-set">
            <div class="rect"></div>
            <div class="gray tac well" ng-if="!helper.obj_length(data)">暂无评论</div>
            <div ng-if="helper.obj_length(data)" ng-repeat="item in data" class="comment-item clearfix">
                <div class="user">[:item.user.username:]:</div>
                <div class="comment-content">
                   [:item.content:]
                </div>
            </div>
{{--
            <div class="comment-item clearfix">
                <div class="user">李明</div>
                <div class="comment-content">
                    会计师；大幅减少了就发了时间浪费大家啊是啦顺丰到付就是打发时间锻炼腹肌司法解释
                </div>
            </div>

            <div class="comment-item clearfix">
                <div class="user">李明</div>
                <div class="comment-content">
                    会计师；大幅减少了就发了时间浪费大家啊是啦顺丰到付就是打发时间锻炼腹肌司法解释
                </div>
            </div>

            <div class="comment-item clearfix">
                <div class="user">李明</div>
                <div class="comment-content">
                    会计师；大幅减少了就发了时间浪费大家啊是啦顺丰到付就是打发时间锻炼腹肌司法解释
                </div>
            </div>--}}
        </div>
    </div>
    <div class="input-group">
        <form ng-submit="_.add_comment()" class="comment_form">
            <input ng-model="Answer.new_comment.content" type="text" placeholder="说些什么...">
            <button class="primary" type="submit">评论</button>
        </form>
    </div>
</script>
</body>
{{--<script type="text/ng-template" id="home.tpl">

</script>--}}

{{--<script type="text/ng-template" id="signup.tpl">


</script>--}}

{{--<script type="text/ng-template" id="login.tpl">

</script>--}}


{{--<script type="text/ng-template" id="question.add.tpl">

</script>--}}
</html>