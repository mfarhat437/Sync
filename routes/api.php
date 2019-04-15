<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    

//Route::get('/test',function(){
    //$id=auth()->user()->id;
   // return response($id);
//})->middleware('auth:api');


Route::group(['middleware'=>'cors'],function(){
//Route::group(['middleware'=>'auth:api', 'except' => ['login','register','events','forget_password','verify_code','fb_login1','fb_login2']],function(){
//------------User Routes-----------//

Route::post('/register','usersController@register')->name('register');
Route::post('/login','usersController@login')->name('login');
Route::get('/edit_user','usersController@edit')->middleware('auth:api');
Route::post('/update_user','usersController@update')->middleware('auth:api');
Route::delete('/delete_user/{id}','usersController@destroy')->middleware('auth:api');

Route::post('/reset_password','ForgotPasswordController')->name('forget_password');
Route::post('/verify_code','usersController@verify_code')->name('verify_code');
Route::post('/change_password','usersController@change_password');

Route::post('/register1','usersController@register_step1');
Route::post('/register2','usersController@register_step2');
Route::post('/register3','usersController@register_step3');
Route::post('/suggested_friends','usersController@post_suggested_friends')->middleware('auth:api');
Route::get('/suggested_friends','usersController@list_suggested_friends')->middleware('auth:api');
Route::get('/user_goings_count','goingsController@user_going_count')->middleware('auth:api');    
Route::get('/most_sync','goingsController@most_sync_users')->middleware('auth:api');    
Route::get('/user_synced_events','usersController@synced_events')->middleware('auth:api');

//------------FriendShips-----------//

Route::post('/send_friend_request','friendshipsController@send_friend_request')->middleware('auth:api');
Route::post('/accept_friend_request','friendshipsController@accept_friend_request')->middleware('auth:api');
Route::post('/deny_friend_request','friendshipsController@deny_friend_request')->middleware('auth:api');
//Route::get('/list_friends_requests','friendshipsController@list_friends_requests');
Route::get('/suggested_friends','usersController@suggested_friends')->middleware('auth:api');

Route::post('/find_friends','friendosController@search_friends');
//Route::post('/add_friends','friendosController@add_friend');
Route::get('/friends_list/{id}','friendshipsController@friends_list');


//------------Event Routes-----------//

Route::get('/events','eventscontroller@index')->name('events');
Route::post('/create_event','eventscontroller@store')->middleware('auth:api');
Route::get('/edit_event/{id}','eventscontroller@edit')->middleware('auth:api');
Route::get('/show_event/{id}','eventscontroller@show')->name('show_event');
Route::post('/update_event/{id}','eventscontroller@update')->middleware('auth:api');
Route::delete('/delete_event/{id}','eventscontroller@destroy')->middleware('auth:api');
Route::get('/event_categories','eventscontroller@categories')->middleware('auth:api');
Route::get('/friends_list','eventscontroller@show_friends')->middleware('auth:api');
    
//Route::post('/create_event1','eventscontroller@create_event1');
//Route::post('/create_event2','eventscontroller@create_event2');
//Route::post('/create_event3','eventscontroller@create_event3');
Route::post('/invite_all_friends','eventscontroller@create_event4');
Route::post('/invit_specific_friends','eventscontroller@create_event5');
    
    
    
    
//--------------Task Routes----------------//

Route::get('/event_tasks/{id}','taskscontroller@index');
Route::post('/create_task','taskscontroller@store')->middleware('auth:api');
Route::get('/edit_task/{id}','taskscontroller@edit')->middleware('auth:api');
Route::post('/update_task/{id}','taskscontroller@update')->middleware('auth:api');
Route::delete('/delete_task/{id}','taskscontroller@destroy')->middleware('auth:api');
Route::post('/choose_task','taskscontroller@choose_task')->middleware('auth:api');

//--------------Event Goings----------------//

Route::post('/going_to_event','goingsController@make_going')->middleware('auth:api');

//--------------Facebook Login----------------//

Route::get('/redirect', 'SocialAuthFacebookController@redirect')->name('fb_login1');
Route::get('/callback', 'SocialAuthFacebookController@callback')->name('fb_login2');


//--------------Invitations----------------//
Route::post('/send_invitation','invitationController@send_invitation')->middleware('auth:api');



//--------------Notifications----------------//
Route::get('/send_notification','eventscontroller@send_notification');
    
    
//});
    
    
});

