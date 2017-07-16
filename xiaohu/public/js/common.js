;(function(){
    'use strict';
    angular.module('common',[])
    .service('TimelineService',[
        '$http',
        'AnswerService',
        function($http,AnswerService){
            var me = this;
            me.data = [];
            me.current_page = 1;
            /*获取首页数据*/
            me.get =function (conf){
                if(me.pending) return;
                me.pending = true;
                conf = conf || {page : me.current_page};
                $http.post('/api/timeline',conf)
                    .then(function(r){
                        if(r.data.status){
                            if(r.data.data.length){
                                me.data = me.data.concat(r.data.data);
                               /* 统计每一条回答的票数*/
                               /*console.log(me.data);*/
                                me.data = AnswerService.count_vote(me.data);
                                me.current_page++;
                            }
                            else
                                me.no_more_data = true;
                        }
                        else
                            console.log('network error');
                    },function(){
                        console.log('network error');
                    })
                    .finally(function(){
                        me.pending = false;
                    })
            };
            /*在时间线中投票*/
            me.vote = function (conf){
                /*调用核心投票功能*/
                AnswerService.vote(conf)
                    .then(function(r){
                        /*如果投票成功 就更新AnswerService中的数据*/
                    if(r)
                        AnswerService.update_data(conf.id);
                    })
            }
        }
    ])

        .controller('HomeController',[
            '$scope',
            'TimelineService',
            'AnswerService',
            function($scope,TimelineService,AnswerService){
                var $win;
                $scope.Timeline = TimelineService;
                TimelineService.get();
                //滚动条事件
                $win = $(window);
                $win.on('scroll',function(){
                    //下拉刷新
                    if($win.scrollTop()-($(document).height()-$win.height())>-30)
                    {
                        TimelineService.get();
                    }
                });
                /*监控回答数据的变化 如果回答数据有变化同时更新其他模块的回答数据*/
                $scope.$watch(function(){
                    return AnswerService.data;
                },function(new_data,old_data){

                   var timeline_data = TimelineService.data;
                   for(var k in new_data){
                       /*更新时间现中的回答数据*/
                       for(var i = 0;i < timeline_data.length; i++){
                           if(k == timeline_data[i].id){
                               //更新点赞数据
                               timeline_data[i] = new_data[k];
                           }
                       }
                   }
                   //实时刷新数据
                    timeline_data = AnswerService.count_vote(TimelineService.data)
                },true)

            }])
})();
