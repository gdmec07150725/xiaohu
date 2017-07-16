;(function(){
    'use strict';
    angular.module('user',['answer'])
        .service('UserService',[
            '$state',
            '$http',
            function ($state,$http){
                var me = this;
                me.signup_data = {};
                me.login_data = {};
                me.data = {};
                me.signup = function (){
                    //调用用注册api
                    $http.post('api/signup',me.signup_data)
                        .then(function(r){
                            if(r.data.status){
                                //注册成功，清空输入框内容
                                me.signup_data = {};
                                //跳回登陆页
                                $state.go('login');
                            }
                        },function (){

                        })
                };
                me.login = function (){
                    $http.post('/api/login',me.login_data)
                        .then(function(r){
                            if(r.data.status){
                                //登陆成功并刷新所有页面
                                location.href = '/';
                            }else{
                                me.login_failed = true;
                            }
                        },function(){

                        })
                };
                me.username_exists = function(){
                    //判断用户是否存在
                    $http.post('api/user/exist',
                        {username:me.signup_data.username})
                        .then(function(r)
                        {
                            if(r.data.status && r.data.count)
                                me.signup_username_exists = true;
                            else
                                me.signup_username_exists = false;
                        },function(e){
                            console.log('e',e);
                        })
                };
                me.read = function (param){
                        return $http.post('/api/user/read',param)
                            .then(function(r){
                                if(r.data.status){
                                    /*if(param.id =='self')
                                        me.self_data = r.data.data;
                                    else
                                         me.data[param.id] = r.data.data;*/
                                    me.current_user = r.data.data;
                                    me.data[param.id] = r.data.data;
                                }else{
                                    if(r.data.msg == "login required")
                                        $state.go('login');
                                }

                            })
                }
            }])

        .controller('SignupController',[
            '$scope',
            'UserService',
            function($scope,UserService)
            {
                $scope.User = UserService;

                $scope.$watch(function(){
                    return UserService.signup_data;
                },function (n,o){
                    if(n.username != o.username)
                        UserService.username_exists();
                },true)
            }
        ])

        .controller('LoginController',[
            '$scope',
            'UserService',
            function( $scope,UserService){
                $scope.User = UserService;
            }

        ])
        .controller('UserController',[
            '$scope',
            '$stateParams',
            'AnswerService',
            'QuestionService',
            'UserService',
            function($scope,$stateParams,AnswerService,QuestionService,UserService){
                $scope.User = UserService;
               /* console.log($stateParams);*/
                //调用查询用户方法
                UserService.read($stateParams);
               AnswerService.read({user_id:$stateParams.id})
                    .then(function(r){
                        if(r){
                            UserService.his_answers = r;
                        }
                    });
                QuestionService.read({user_id:$stateParams.id})
                    .then(function(r){
                        if(r){
                            UserService.his_questions = r;
                        }
                    })

        }])
})();