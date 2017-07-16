<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
class User extends Model
{
    //注册api
    public  function  signup()
    {
      /*dd(Request::get('password'));
      dd(Request::has('age'));
      dd(Request::all());*/
      /*检查用户名和密码是否为空*/
   /*   $username=Request::get('username');
      $password=Request::get('password');
      if(!($username && $password))
          return['status'=>0,'msg'=>'用户名或密码不能为空'];*/
        $has_username_and_password=$this->has_username_and_password();
        if(!$has_username_and_password)
            return ['status'=>0,'msg'=>'username and password are required'];
            $username=$has_username_and_password[0];
            $password=$has_username_and_password[1];

      /*检查用户名是否存在*/
      $user_exists = $this
          ->where('username',$username)
          ->exists();

      if($user_exists)
          return['status'=>0,'msg'=>'user had exists'];

        /*加密密码*/
        /*$hashed_password = Hash::make('$password');*/
       /* $encrypted = Crypt::encryptString($password);*/
        $encrypted=encrypt($password);
        /*存入数据库*/
        $user = $this;
        $user->password = $encrypted;
        $user->username = $username;
        if($user->save())
        {
             return ['status' =>1, 'id'=>$user->id];
        }else{
            return ['status' =>0, 'msg'=>'db insert failed'];
        }
    }

    /*获取用户信息*/
    public function read(){
        if(!rq('id'))
            return ['status'=>0,'msg'=>'required id'];

        if(rq('id')==='self'){
            if(!$this->is_logged_in())
                return ['status'=>0,'msg'=>'login required'];
            $id = session('user_id');
        }else
               $id = rq('id');
        $get = ['id','username','avatar_url','intro'];
        $user = $this->find($id,$get);
        if(!$user)
            return ['status'=>0,'msg'=>'user not exists'];
        $data = $user->toArray();
       /* $answer_count = $user->answers()->count();
        $question_count = $user->questions()->count();*/
       $answer_count = answer_ins()->where('user_id',$id)->count();
       $question_count = question_ins()->where('user_id',$id)->count();

       $data['answer_count'] = $answer_count;
       $data['question_count'] = $question_count;

       return ['status'=>1,'data'=>$data];

    }


    //登陆api
    public function login(){

        /*dd(session('abc','cde'));*/
        //检查用户名和密码是否存在
        $has_username_and_password =$this->has_username_and_password();
        if(!$has_username_and_password)
            return ['status'=>0,'msg'=>'username and password are required'];

        $username=$has_username_and_password[0];
        $password=$has_username_and_password[1];
        //检查数据库是否有$username这个用户名
        $user = $this->where('username',$username)->first();
        if(!$user)
            return ['status'=>0,'msg'=>'user not exists'];


        //检查密码是否正确
        $hashed_password =$user->password;
        //解密
        /*$decrypted = Crypt::decryptString($hashed_password);*/

        try
        {
            $decrypted = decrypt($hashed_password);
            if($decrypted != $password)
                return ['status'=>0,'msg' =>'invalid password'];

            //将用户名添加到session里面
            session()->put('username',$user->username);
            //将id添加到session里面
            session()->put('user_id',$user->id);
            return ['status'=>1,'id '=>$user->id];
        } catch (DecryptException $e)
        {
            return ['status'=>0,'msg'=>'login failed'];
        }
/*        if(!$user){
            return ['status' =>0,'msg' => 'user not exists'];
        }else {
            //检查密码是否正确
            $hashed_password = $user->password;
            if (Hash::check($password, $hashed_password)){
                //将用户名添加到session里面
                session()->put('username',$user->username);
                //将id添加到session里面
                session()->put('user_id',$user->id);
                //查看session信息
                dd(session()->all());
                return ['status'=>1,'id '=>$user->id];
            }else{

                return ['status' => 0, 'msg' => 'invalid password'];
            }

        }*/
    }
    //获取用户输入信息
    public function has_username_and_password(){
        $username=rq('username');
        $password=rq('password');
        /*检查用户名和密码是否为空*/
        if($username && $password)
        return [$username,$password];

        return false;
    }
    //登出api
    public function logout(){
        //清空session的用户名和id
        session()->forget('username');
        session()->forget('user_id');
       /* session()->put('username',null);
        session()->put('user_id',null);*/
      /*  dd(session()->all());*/
      /*  return ['status'=>1];*/
        //跳回首页
        return redirect('/');

    }

    //检查用户是否登陆
    public function  is_logged_in(){
        //如果session中存在user_id就返回user_id，否则返回false
        return is_logged_in();
    }

    //修改密码api
    public function change_password (){
        if(!$this->is_logged_in())
            return ['status' =>0,'msg' =>'login required'];

        if(!rq('old_password')|| !rq('new_password'))
            return ['status' =>0,'msg'=>'old_password and new_password are required'];

        $user= $this->find(session('user_id'));

        //判断旧密码是否正确
        $old_password=$user->password;
        //解密
        /*$decryptedpassword = Crypt::decryptString($old_password);*/
        try {
            $decryptedpassword = decrypt($old_password);
            if($decryptedpassword != rq('old_password'))
                return ['status' =>0,'msg' =>'invalid old_password'];

            //保存新密码
            /*$user->password=bcrypt(rq('new_password'));*/
            $user->password=encrypt(rq('new_password'));
            return $user->save()?
                ['status' =>1]:
                ['status' =>0 ,'msg' =>'db update failed'];
        } catch (DecryptException $e){

        }

    }
    //找回密码api
    public function reset_password(){

        if($this->is_robot())
            return ['status'=>0,'msg'=>'max frequency reached'];

       if( !rq('phone'))
           return ['status'=>0,'msg'=>'phone is required'];
       $user = $this->where('phone',rq('phone'))->first();
       /* $user = $this->where('username',$username)->first();*/

       if(!$user)
           return ['status'=>0,'msg'=>'invalid phone number'];
            //生成验证码
            $captcha = $this->generate_captcha();
            $user->phone_captcha = $captcha;
            if($user->save())
            {
                //如果验证码保存成功，发送验证码
                $this->send_sms();
                 //为下一次机器人调用检查做准备
               $this->update_robot_time();
                return ['status'=>1];
            }
                return ['status'=>0,'msg'=>'db insert phone failed'];
    }

    //验证找回密码api
    public function validate_reset_password(){
        if($this->is_robot(2))
            return ['status'=>0,'msg'=>'max frequency reached'];

         if(!rq('phone')||!rq('phone_captcha')||!rq('new_password'))
             return ['status'=>0,'msg'=>'phone and phone_captcha are required'];
        //检查用户是否存在
         $user=$this->where([
             'phone'=>rq('phone'),
             'phone_captcha'=>rq('phone_captcha')
         ])->first();

         if(!$user)
             return ['status'=>0,'msg'=>'invalid phone or invalid phone_captcha'];
         //加密新密码
        $encrypted=encrypt(rq('new_password'));
         $user->password = $encrypted;
         $this->update_robot_time();
         return $user->save()?
             ['status'=>1]:
             ['status'=>0,'msg'=>'db update failed'];
    }
    //检查机器人
    public function is_robot($time = 10){
         /*如果session没有last_sms__time说明接口从未被调用过*/
        if(!session('last_action_time'))
            return false;

        $current_time = time();
        $last_active_time = session('last_action_time');

        $elapsed = $current_time -$last_active_time;
        return !($elapsed > $time);

    }
    //更新机器人行为时间
    public  function update_robot_time(){
       /* session()->set('last_sms_time',time());*/
        session(['last_action_time' => time()]);
    }
        //发送短信
    public function send_sms(){
       return  true;
    }
    //生成验证码
    public function generate_captcha ()
    {
        return rand(1000,9999);
    }


    public function answers()
    {
        return $this
            ->belongsToMany('App\Answer')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function questions()
    {
        return $this
            ->belongsToMany('App\Question')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function exist()
    {
        return ['status'=>1,'count'=>$this->where(rq())->count()];
    }
}
