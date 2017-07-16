<div ng-controller="QuestionAddController" class="question_add container">
    <div class="card">
        <form name="question_add_form" ng-submit="Question.add()">
            <div class="input-group">
                <lable>问题标题</lable>
                <input name="title" type="text" ng-minlength="5" ng-maxlength="255" ng-model="Question.new_question.title" required>
            </div>

            <div class="input-group">
                <lable>问题描述</lable>
                <textarea name="desc" type="text" ng-model="Question.new_question.desc">
                    </textarea>
            </div>

            <div class="input-group">
                <button  class="primary" ng-disabled="question_add_form.$invalid" type="submit">提交</button>
            </div>

        </form>
    </div>
</div>