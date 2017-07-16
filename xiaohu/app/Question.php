<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    public function add(){

        //判断用户是否登陆
        if(!user_ins()->is_logged_in())
            return ['status'=>0, 'msg'=>'login required'];
        //检查是否存在标题
        if(!rq('title'))
            return ['status'=>0,'msg' =>'required title'];

        $this->title = rq('title');
        $this->user_id = session('user_id');
        //检查是否存在描述，如果存在就添加到数据库
        if(rq('desc'))
            $this->desc = rq('desc');

        //保存
        return $this->save() ?
            ['status' => 1, 'id' =>$this->id] :
            ['status' =>0, 'msg' =>'db insert failed'];

    }
    //更新问题api
    public function change(){
        //判断用户是否登陆
        if(!user_ins()->is_logged_in())
            return ['status'=>0, 'msg'=>'login required'];

        if(!rq('id'))
            return ['status'=>0,'msg'=>'id is required'];
        //查找当前操作的id的那条数据
        $question = $this->find(rq('id'));
        //判断问题是否存在
            if(!$question)
              return ['status' => 0,'msg' => 'question  not exists'];
        //如果当前用户不是问题的创建者
        if($question->user_id != session('user_id'))
            return ['status' => 0,'msg' => '你不是问题的创建者，不能修改问题'];

        //修改当前问题的title和desc
            if(rq('title'))
                $question->title = rq('title');

            if(rq('desc'))
                $question->desc = rq('desc');
            //保存
            return $question->save()?
                ['status' => 1]:
                ['status' => 0, 'msg' => 'db update failed'];

    }
    public function read_by_user_id($user_id){
        $user = user_ins()->find($user_id);
        if(!$user)
            return ['status'=>0,'msg'=>'user not exists'];

        $r = $this->where('user_id',$user_id)
            ->get()->keyBy('id');
        return ['status'=>1,'data'=>$r->toArray()];
    }
    //查看问题api
    public function read() {
        //请求参数中是否有id,如果有id就直接返回id所在的行
        if(rq('id')){
            $r = $this
                ->with('answers_with_user_info')
                ->find(rq('id'));
            return ['status' =>1,'data'=> $r->toArray()];
        }
        if(rq('user_id')){
            $user_id = rq('user_id') =='self'?
                session('user_id'):
                rq('user_id');

            return $this->read_by_user_id($user_id);
        }
        //limit条件 限制查看条数
        $limit= rq('limit')?:15;
        // skip条件，用于分页
       /* $skip = (rq('page') ? rq('page') -1 : 0)*$limit;*/
        list($limit,$skip) = paginate(rq('page'), rq('limit'));
        /*构建query并返回collection数据*/
        $r = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(['id','title','desc','user_id','created_at','updated_at'])
            ->keyBy('id');

        return ['status' =>1, 'data' => $r];
    }
    //删除问题api
    public function remove(){
        //检查用户是否登录
        if(!user_ins()->is_logged_in())
            return ['status' => 0, 'msg'=>'login required'];
        //检查传参是否有id
        if(!rq('id'))
            return ['status'=>0,'msg'=>'id is required'];

        //获取传参id所对应的model
        $question = $this->find(rq('id'));
        if(!$question) return ['status'=>0, 'question not exists'];

        //检查当前用户是否为问题的所有者
        if(session('user_id') !=$question->user_id)
            return ['status'=> 0,'msg'=>'permission denied'];


         //删除
         return  $question->delete() ?
             ['status'=> 1]:
             ['status' =>0, 'msg'=>'db deleted failed'];



    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function answers(){
        return $this->hasMany('App\Answer');
    }
    public function  answers_with_user_info(){
        return $this
            ->answers()
            ->with('user')
            ->with('users');
    }
}
