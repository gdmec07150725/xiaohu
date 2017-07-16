<div ng-controller="LoginController" class="login container">
    <div class="card">
        <h1>登陆</h1>
        <form name="login_form" ng-submit="User.login()">
            <div class="input-group">
                <lable>用户名</lable>
                <input name="username" type="text" ng-model="User.login_data.username" required>
            </div>

            <div class="input-group">
                <lable>密码</lable>
                <input name="password" type="password" ng-model="User.login_data.password" required>
                <div ng-if="User.login_failed" class="input-error-set">
                    用户名或密码有误
                </div>
            </div>
            <button  class="primary" type="submit" ng-disabled="login_form.username.$error.required || login_form.password.$error.required">登陆</button>
        </form>
    </div>
</div>
