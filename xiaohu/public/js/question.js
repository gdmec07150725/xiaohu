;(function(){
    'use strict';
    angular.module('question',[])
        .service('QuestionService',[
            '$http',
            '$state',
            'AnswerService',
            function($http,$state,AnswerService){
                var me = this;
                me.new_question = {};
                me.data = {};
                me.update = function () {
                    if(!me.current_question.title){
                        return false;
                        console.error('title is required !');
                    }
                    return $http.post('/api/question/change',me.current_question)
                        .then(function (r){
                           if(r.data.status)
                                me.show_update_form = false;
                        })
                };

                me.go_add_question = function(){
                    $state.go('question.add');
                };
                me.read = function (params){
                    return  $http.post('/api/question/read',params)
                    .then(function(r){

                        if(r.data.status){
                            if(params.id){
                                me.data[params.id] = me.current_question = r.data.data;
                                me.its_answers = me.current_question.answers_with_user_info;
                                me.its_answers = AnswerService.count_vote(me.its_answers);
                                console.log(me.its_answers);
                            }else{
                                me.data = angular.merge({},me.data,r.data.data);
                            }
                            return r.data.data;
                        }else{
                            return false;
                        }
                    })
                };
                me.vote = function(conf){
                    //调用核心投票功能
                    AnswerService.vote (conf)
                        .then (function (r) {
                            if(r){
                                me.update_answer(conf.id);
                            }
                        })
                };
                me.update_answer = function (answer_id){
                    $http.post('/api/answer/read' ,{id:answer_id})
                        .then(function(r){
                        if(r.data.status){
                            for(var i = 0;i<me.its_answers.length;i++){
                                var answer = me.its_answers[i];
                                if(answer.id == answer_id) {
                                    console.log(r.data.data);
                                    me.its_answers[i] =r.data.data;
                                    AnswerService.data[answer_id] = r.data.data;
                                }
                            }
                        }

                        })
                };
                me.add = function () {
                    if(!me.new_question.title)
                        return;
                    $http.post('/api/question/add',me.new_question)
                        .then(function(r){
                            if(r.data.status){
                                //添加成功，清空输入框内容
                                me.new_question={};
                                //跳回首页
                                $state.go('home');
                            }
                        },function (e){

                        })
                }
            }
        ])
        .controller('QuestionController',[
            '$scope',
            'QuestionService',
            function($scope,QuestionService){
                $scope.Question = QuestionService;
            }
        ])
        .controller('QuestionAddController',[
            '$scope',
            '$state',
            'QuestionService',
            function($scope,$state,QuestionService){
                $scope.Question = QuestionService;
                if(!his.id)
                    $state.go('login');
            }
        ])
        .controller('QuestionDetailController',[
            '$scope',
            '$stateParams',
            'AnswerService',
            'QuestionService',
            function($scope,$stateParams,AnswerService,QuestionService){
                $scope.Answer = AnswerService;
                $scope.Question = QuestionService;
                QuestionService.read($stateParams);
                if($stateParams.answer_id)
                    QuestionService.current_answer_id = $stateParams.answer_id;
                else{
                    QuestionService.current_answer_id = null;
                }
            }
        ])
})();

