<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
function paginate($page =1,$limit = 16)
{
    $limit = $limit ?: 16;
    $skip = ($page ? $page -1 : 0)*$limit;
    return [$limit,$skip];
}

function rq($key=null,$default=null){
    if(!$key)return Request::all();
    return Request::get($key,$default);
}
function user_ins(){
    return  new App\User;
}

function question_ins(){
    return  new App\Question;
}
function answer_ins(){
    return  new App\Answer;
}

function comment_ins(){
    return  new App\Comment;
}

//检查用户是否登陆
 function  is_logged_in(){
    //如果session中存在user_id就返回user_id，否则返回false
    return session ('user_id') ?: false;
}

Route::get('/', function () {
    return view('index');
});

Route::any('api',function(){
    return ['version'=>0.1];
});
//注册
Route::any('api/signup',function(){

    return user_ins()->signup();
});
//登陆
Route::any('api/login',function(){

    return user_ins()->login();
});
//登出
Route::any('api/logout',function(){

    return user_ins()->logout();
});

//修改密码api
Route::any('api/user/change_password',function(){

    return user_ins()->change_password();
});

//检查用户是否存在
Route::any('api/user/exist',function(){

    return user_ins()->exist();
});

Route::any('api/user/validate_reset_password',function(){

    return user_ins()->validate_reset_password();
});
Route::any('api/user/read',function(){

    return user_ins()->read();
});

//找回密码
Route::any('api/user/reset_password',function(){

    return user_ins()->reset_password();
});

//添加问题api
Route::any('api/question/add',function(){

    return question_ins()->add();
});
//更新问题api
Route::any('api/question/change',function(){

    return question_ins()->change();
});
//查看问题api
Route::any('api/question/read',function(){

    return question_ins()->read();
});
//删除问题api
Route::any('api/question/remove',function(){

    return question_ins()->remove();
});

//回答api
Route::any('api/answer/add',function(){

    return answer_ins()->add();
});

//更新回答api
Route::any('api/answer/change',function(){

    return answer_ins()->change();
});

//查看回答api
Route::any('api/answer/read',function(){

    return answer_ins()->read();
});
//删除回答api
Route::any('api/answer/remove',function(){

    return answer_ins()->remove();
});

Route::any('api/answer/vote',function(){

    return answer_ins()->vote();
});

//添加评论api
Route::any('api/comment/add',function(){

    return comment_ins()->add();
});

//查看评论api
Route::any('api/comment/read',function(){

    return comment_ins()->read();
});

//删除评论api
Route::any('api/comment/remove',function(){

    return comment_ins()->remove();
});

//时间线api
Route::any('api/timeline','CommonController@timeline');


//检查用户是否登陆
Route::any('test',function(){

    dd( user_ins()->is_logged_in());
});

Route::get('tpl/page/home',function(){
   return view('page.home');
});

Route::get('tpl/page/signup',function(){
    return view('page.signup');
});

Route::get('tpl/page/login',function(){
    return view('page.login');
});

Route::get('tpl/page/question_add',function(){
    return view('page.question_add');
});

Route::get('tpl/page/user',function(){
    return view('page.user');
});

Route::get('tpl/page/question_detail',function(){
    return view('page.question_detail');
});