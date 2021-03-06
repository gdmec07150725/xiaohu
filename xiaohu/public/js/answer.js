;(function(){
    'use strict';
    angular.module('answer',[])
        .service('AnswerService',[
            '$http',
            '$state',
            function($http,$state){
            var me = this;
            me.data = {};
            me.answer_form = {};
           /* 统计票数*/
            me.count_vote = function(answers){
                /*迭代所有的数据*/
            for(var i = 0; i< answers.length; i++){
               /* 封装单个数据*/
                /*var votes, item = answers[i];*/
                var votes, item = answers[i];
                if(!item['question_id'])
                    continue;
                me.data[item.id]=item;

                if(!item['users'])
                    continue;

                item.upvote_count = 0;
                item.downvote_count = 0;

                votes = item['users'];

                for(var j =0 ;j < votes.length;j++){
                    var v = votes[j];

                    if(v['pivot'].vote === 1)
                        item.upvote_count++;
                    if(v['pivot'].vote === 2)
                        item.downvote_count++;

                }
            /*    /!*如果不是回答也没有users元素说明本条不是回答或回答没有任何票数*!/
               if(!item['question_id'])
                    continue;
                me.data[item.id]=item;

                if(!item['users'])
                    continue;
                /!*每条回答的默认赞同票和反对票都为0*!/
                item.upvote_count = 0;
                item.downvote_count = 0;
               /!* users是所有投票用户的信息*!/
                votes = item['users'];
                /!*console.log(item.users);*!/
                for(var j =0 ;j < votes.length;j++){
                    var v = votes[j];
                   /!* 获取pivot元素 中所有用户投票信息
                    如果是1将增加赞同票
                    如果是2将增加反对票*!/
                    if(v['pivot'].vote === 1)
                        item.upvote_count++;
                    if(v['pivot'].vote === 2){
                        item.downvote_count++;
                    }
                }
                return answers;*/
                return answers;
            }
            };
            me.add_or_update = function (question_id){
                if(!question_id){
                    console.log('question_id is required');
                    return;
                }
                me.answer_form.question_id = question_id;
                if(me.answer_form.id)
                    $http.post('/api/answer/change',me.answer_form)
                        .then(function (r) {
                            if(r.data.status){
                                //清空文本框
                                me.answer_form = {};
                                $state.reload();
                            }
                        });
                else{
                    $http.post('/api/answer/add',me.answer_form)
                        .then(function (r) {
                            if(r.data.status){
                                me.answer_form = {};
                                $state.reload();
                            }
                        });
                }
            };
            me.delete = function (id){
                if(!id){
                    console.log('id is required');
                    return;
                }
                $http.post('/api/answer/remove' ,{id:id})
                    .then(function (r) {
                        if(r.data.status){
                            console.log('deleted successfully');
                            //刷新页面
                            $state.reload();
                        }
                    })
            };
            me.vote = function (conf)
                {
                    if(!conf.id || !conf.vote)
                    {
                        console.log('id and vote are required');
                        return;
                    }
                   //取消点赞
                    var answer = me.data[conf.id],
                        users = answer.users;

                    //判断当前用户是否投过相同的票
                    for(var i = 0; i < users.length;i++){
                        if(users[i].id == his.id && conf.vote == users[i].pivot.vote)
                            conf.vote = 3;
                    }

                    return $http.post('api/answer/vote',conf)
                        .then(function(r){
                            if(r.data.status){
                                return true;
                            }else if(r.data.msg == 'login is required'){
                                $state.go('login');
                            }else{
                                return false;
                            }

                        },function (){
                            return false;
                        })
                };
                me.update_data = function(id)
                {
                return $http.post('/api/answer/read',{id: id})
                    .then(function (r){
                        me.data[id] = r.data.data;
                    })
                 /*   if(angular.isNumber(input))
                        var id = input;
                    if(angular.isArray(input))
                        var id_set = input;*/
                };
                me.read = function (params){
                   return $http.post('/api/answer/read',params)
                       .then(function(r){
                           if(r.data.status){
                               me.data = angular.merge({},me.data,r.data.data);
                               return r.data.data;
                           }else{
                               return false;
                           }
                       })
                };
                me.add_comment = function (){
                    return $http.post('/api/comment/add',me.new_comment)
                        .then(function (r){
                           if(r.data.status)
                               return true;
                           return false;
                        })
                }

        }])
                .directive('commentBlock',[
                    'AnswerService',
                    '$http',
                    function(AnswerService,$http){
                        var o = {};
                        o.templateUrl = 'comment.tpl';
                        o.scope = {
                            answer_id: '=answerId'
                        };
                        o.link = function (sco, ele,attr) {
                            sco.Answer = AnswerService;
                            sco._ = {};
                            sco.data =  {};
                            sco.helper = helper;
                            /*ele.on('click',function(){*/
                            function get_comment_list (){
                                return  $http.post('/api/comment/read' ,
                                    {answer_id:sco.answer_id})
                                    .then(function (r){
                                        if(r.data.status)
                                            sco.data = angular.merge({},sco.data,r.data.data);
                                    });
                            }
                                if(sco.answer_id)
                                    get_comment_list();

                          /*  });*/
                            sco._.add_comment = function (){
                                AnswerService.new_comment.answer_id = sco.answer_id;
                                AnswerService.add_comment()
                                    .then(function(r){
                                        if(r){
                                            AnswerService.new_comment = {};
                                            get_comment_list()
                                        }
                                    })
                            }
                        };
                        return o;
                    }
                ])
})();
