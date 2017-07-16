<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //
    public function add(){
        //检查用户是否登陆
       if(!user_ins()->is_logged_in())
            return  ['status' =>0, 'msg' =>'login required'];

        //检查参数中是否存在questio_id和content
       if(!rq('question_id') || !rq('content'))
           return ['status' =>0, ';msg' =>'question_id and content are required'];

       $question = question_ins()->find(rq('question_id'));
       if(!$question) return ['status' =>0,'msg' =>'question not exists'];

        //检查是否重复回答
       $answer= $this
            ->where(['question_id' =>rq('question_id'),'user_id' =>session('user_id')])
            ->count();
            if($answer)
                return ['status' =>0, 'msg'=>'duplicate answers'];

        //保存数据
       $this->content=rq('content');
       $this->question_id =rq('question_id');
       $this->user_id = session('user_id');

       return $this->save()?
           ['status' => 1, 'id' => $this->id]:
           ['status' =>0,'msg' =>'db insert failed'];

    }
    public function change (){
        //检查用户是否登陆
        if(!user_ins()->is_logged_in())
            return  ['status' =>0, 'msg' =>'login required'];
        //检查手否有id和content
        if(!rq('id') || !rq('content'))
            return ['status' =>0,'msg'=>'id and content are required'];
        //查找id对应的记录
        $answer = $this->find(rq('id'));
        //判断回答是否存在
        if(!$answer)
            return ['status'=>0,'msg'=>'answer not exists'];

        if($answer->user_id !=session('user_id'))
            return ['status' =>0, 'msg' =>'permission denied'];

        //保存
        $answer->content = rq('content');
        return $answer->save() ?
            ['status' =>1]:
            ['status' =>0 ,'msg'=>'db ;update failed'];

    }
    public function read_by_user_id($user_id){
        $user = user_ins()->find($user_id);
        if(!$user)
            return ['status'=>0,'msg'=>'user not exists'];

           $r = $this
               ->with('question')
               ->where('user_id',$user_id)
               ->get()
               ->keyBy('id');
           return ['status'=>1,'data'=>$r->toArray()];
    }
    //查看回答api
    public  function read () {

        if( !rq('id') && !rq('question_id') && !rq('user_id'))
            return ['status' =>0 ,'msg'=>'id  user_id and question_id  is required'];

        if(rq('user_id'))
        {
            $user_id = rq('user_id') === 'self'?
                session('user_id'):
                rq('user_id');
            return $this->read_by_user_id($user_id);
       }
        if(rq('id')){
            //查看单个回答
            $answer = $this
                ->with('user')
                ->with('users')
                ->find(rq('id'));
            /*dd($answer->toArray());*/
                if(!$answer)
                    return ['status' =>0,'msg'=>'answer not exists'];
                //调用统计票数方法
                $answer = $this->count_vote($answer);
                return ['status' =>1,'data' =>$answer];
        }
        //在查看回答前，检查问题是否存在
        if(!question_ins()->find(rq('question_id')))
            return ['status'=>0,'msg' =>'question not exists'];

        //查看同一问题的所有回答
         $answers =$this
            ->where('question_id',rq('question_id'))
            ->get()
            ->keyBy('id');

        return ['status' =>1, 'data'=>$answers];
    }
    public function count_vote ($answer){
        $upvote_count = 0;
        $downvote_count = 0;
        foreach($answer->users as $user){
         if($user->pivot->vote==1)
            $upvote_count++;
         else
             $downvote_count++;
        }
        $answer->upvote_count = $upvote_count;
        $answer->downvote_count = $downvote_count;
        return $answer;
}
    /*投票api*/
    public function vote (){
       if(!user_ins()->is_logged_in())
           return ['status' =>0,'msg'=>'login is required'];

       if(!rq('id') || !rq('vote'))
           return ['status' =>0,'msg'=>'id and vote are requied'];

       $answer = $this->find(rq('id'));
       if(!$answer) return ['status' =>0,'msg'=>'answer not exits'];

       /*1为赞同，2为反对，3为清空*/
       $vote =rq('vote');
       if($vote !=1 && $vote !=2 && $vote !=3)
           return ['status'=>0,'msg'=>'invalid vote'];
       //检查此用户是否在相同问题下投过票,如果投过票，删除投票结果
        $answer->users()
            ->newPivotStatement()
            ->where('user_id',session('user_id'))
            ->where('answer_id',rq('id'))
            ->delete();

        if($vote == 3)
            return ['status' =>1];

        //在连接表上添加数据
        $answer
            ->users()
            ->attach(session('user_id'),['vote'=>$vote]);

        return ['status' => 1];
    }
    //删除回答api
    public function remove(){
        if(!user_ins()->is_logged_in())
            return ['status' =>0, 'msg'=>'login is required'];

        if(!rq('id'))
            return  ['status'=>0,'msg'=>'id is required'];

        $answer = $this->find(rq('id'));
        if(!$answer) return ['status'=>0,'msg'=>'answer not exists'];


        //删除回答
        return $answer->delete() ?
            ['status'=>1]:
            ['status'=>0,'msg'=>'db delete failed'];

    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function users()
    {
        return $this
            ->belongsToMany('App\User')
            ->withPivot('vote')
            ->withTimestamps();
    }
    public function question(){
        return  $this->belongsTo('App\Question');
    }
}
