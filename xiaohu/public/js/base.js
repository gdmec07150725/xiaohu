/**
 * Created by Lng888 on 2017/6/16.
 */
;(function(){

    'use strict';
    //获取用户id
    window.his ={
      id:parseInt($('html').attr('user-id'))
    };
    window.helper = {};
    helper.obj_length = function (obj){
        if(obj)
        return Object.keys(obj).length;
    };
    //指定模块名称
    angular.module('xiaohu',[
        'ui.router',
        'common',
        'question',
        'user',
        'answer'
    ])
        //防止angular和laravel语法解析{{变量}}冲突
        .config([
            '$interpolateProvider',
            '$stateProvider',
            '$urlRouterProvider',
        function ($interpolateProvider,$stateProvider, $urlRouterProvider)
    {
        $interpolateProvider.startSymbol('[:');
        $interpolateProvider.endSymbol(':]');

        $urlRouterProvider.otherwise('/home');
        $stateProvider
            .state('home',{
                url:'/home',
                templateUrl:'/tpl/page/home'
            })
            .state('signup',{
                url:'/signup',
                templateUrl:'/tpl/page/signup'
            })
            .state('login',{
                url:'/login',
                templateUrl:'/tpl/page/login'
            })
            .state('question',{
                //抽象的路由，不能直接在地址栏访问
                abstract:true,
                url:'/question',
                template:'<div ui-view></div>'
            })
            .state('question.detail',{
                url:'/detail/:id?answer_id',
                templateUrl:'/tpl/page/question_detail'
            })

            .state('question.add',{
                url:'/add',
                templateUrl:'/tpl/page/question_add'
            })

            .state('user',{
                url:'/user/:id',
                templateUrl:'/tpl/page/user'
            })

    }])
        //全局作用，获取当前用户id
        .controller('BaseController',[
            '$scope',
            function ($scope) {
                $scope.his = his;
                $scope.helper = helper;

            }
        ])

   /*     .controller('TestController',function($scope){

            $scope.name = 'hello world';

        })*/









})();