
<div ng-controller="HomeController" class="home card container">
        <h1>最新动态</h1>
        <div  class="hr"></div>
            <div class="item-set">
                <div ng-repeat="item in Timeline.data" class="feed item clearfix">
                    <div ng-if="item.question_id" class="vote">
                        <div ng-click="Timeline.vote({id:item.id, vote:1})" class="up">赞[: item.upvote_count :]</div>
                        <div ng-click="Timeline.vote({id:item.id, vote:2})" class="down">踩[: item.downvote_count :]</div>
                    </div>
                    <div class="feed-item-content">
                        <div ng-if="item.question_id" class="content-act">
                            <span ui-sref="user({id: item.user.id})">[:item.user.username:]添加了回答</span>
                        </div>
                        <div ng-if="!item.question_id" class="content-act">
                            <span ui-sref="user({id: item.user.id})">[:item.user.username:]添加了提问</span>
                        </div>
                        <div ng-if="item.question_id">[:item.question.title:]</div>
                        <div class="content-title">
                            <span  ui-sref="question.detail({id:item.id})" >[: item.title :]</span>
                        </div>
                        <div class="content-owner">[:item.user.username:]</div>
                        <div ng-if="item.question_id" class="content-main">
                            [: item.content :]
                            <div class="gray">
                                <span ui-sref="question.detail({id:item.question_id,answer_id:item.id})">[:item.updated_at:]</span>
                            </div>

                        </div>
                        <div class="action-set">
                              <span ng-click="item.show_comment = !item.show_comment" class="gray anchor">
                                        <span ng-if="item.show_comment">取消</span>评论
                              </span>
                        </div>
                        <div ng-if="item.show_comment" comment-block answer-id="item.id">

                        </div>
                    </div>
                    <div class="hr"></div>
                </div>
                <div ng-if="Timeline.pending" class="tac">加载中...</div>
                <div ng-if="Timeline.no_more_data" class="tac">没有更多数据了</div>
            </div>
    </div>