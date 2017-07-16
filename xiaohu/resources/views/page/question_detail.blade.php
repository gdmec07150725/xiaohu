<div ng-controller="QuestionDetailController" class="container question-detail">
    <div class="card">
        <h1>[:Question.current_question.title:]</h1>
        <div class="desc">[:Question.current_question.desc:]</div>
        <small ng-if="his.id == Question.current_question.user_id" class="gray anchor" ng-click="Question.show_update_form = !Question.show_update_form"><span ng-if="Question.show_update_form">取消</span>修改问题</small>
        <form ng-if="Question.show_update_form" name="question_add_form" ng-submit="Question.update()" class="well gray_card">
            <div class="input-group">
                <lable>问题标题</lable>
                <input name="title" type="text" ng-minlength="5" ng-maxlength="255" ng-model="Question.current_question.title" required>
            </div>

            <div class="input-group">
                <lable>问题描述</lable>
                <textarea name="desc" type="text" ng-model="Question.current_question.desc">
                    </textarea>
            </div>

            <div class="input-group">
                <button  class="primary" ng-disabled="question_add_form.$invalid" type="submit">提交</button>
            </div>

        </form>
        <div class="gray">回答数:[:Question.current_question.answers_with_user_info.length:]</div>
        <div class="hr"></div>
        <div class="feed item clearfix">
            <div ng-if="!helper.obj_length(Question.current_question.answers_with_user_info)">
            <div class="gray tac well">
                还没有回答，快来抢沙～～
            </div>
            <div class="hr">
            </div>
            </div>
            <div ng-if="!Question.current_answer_id ||Question.current_answer_id == item.id" ng-repeat="item in Question.current_question.answers_with_user_info">
                <div class="vote">
                    <div ng-click="Question.vote({id:item.id, vote:1})" class="up">赞[: item.upvote_count :]</div>
                    <div ng-click="Question.vote({id:item.id, vote:2})" class="down">踩[: item.downvote_count :]</div>
                </div>
                <div class="feed-item-content">
                <div>
                    <span ui-sref="user({id:item.user.id})">[:item.user.username:]</span>
                </div>
                <div>
                    [:item.content:]
                    <div class="action-set">
                        <span ng-click="item.show_comment = !item.show_comment" class="anchor gray">
                            <span ng-if="item.show_comment">取消</span>评论</span>
                    <span class="gray">
                        <span  class="anchor" ng-click="Answer.answer_form = item" ng-if="item.user_id == his.id">编辑</span>
                        <span  class="anchor" ng-click="Answer.delete(item .id)" ng-if="item.user_id == his.id">删除</span>
                        <span>[:item.updated_at:]</span>
                    </span>
                </div>
                </div>
                <div class="hr"></div>
                </div>
                <div ng-if="item.show_comment" comment-block answer-id="item.id">

                </div>
            </div>
        </div>
        <div>
            <form ng-submit="Answer.add_or_update(Question.current_question.id)" name="answer_form" class="answer_form">
                <div class="input-group">
                    <textarea type="text" name="content"  ng-model="Answer.answer_form.content" placeholder="添加回答" required></textarea>
                </div>
                <div class="input-group">
                    <button ng-disabled="answer_form.$invalid" class="primary" type="submit">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>