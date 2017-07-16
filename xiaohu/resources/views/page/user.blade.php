<div ng-controller="UserController">

<div class="user card container">
    <h1>用户详情</h1>
    <div class="hr"></div>
    <div class="basic">
        <div class="info_item clearfix">
            <div class="username">username</div>
            <div>[:User.current_user.username:]</div>
        </div>
        <div class="info_item clearfix">
            <div class="username">intro</div>
            <div>[:User.current_user.intro || '暂无介绍':]</div>
        </div>
    </div>
    <h2>用户提问</h2>
    <div ng-repeat="(key, value) in User.his_questions">
        [:value.title:];
    </div>
    <h2>用户回答</h2>
     <div ng-repeat="(key, value) in User.his_answers">
        <div class="user container title">[:value.question.title:]</div>
        [:value.content:];
        <div class="action-set">更新时间:[:value.question.updated_at:]</div>
    </div>
</div>
</div>
