<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Friend_User;
use DB;
use Log;
class friendosController extends Controller
{
    public function search_friends(Request $request){
            $friends_mobs= $request->json()->all();
//        $mob=$friends_mobs['mobile'];
//Log::debug($request);
//         $array=json_decode($friends_mobs,true);

        
        dd($mob);
            $data=explode(',',$friends_mobs);

//        $data=array("0134832574","01348325744","79789898979");
        
        $count=count($data);
        $output=[];
        $name='';
        $phone='';
        $user_id='';
$friend_id;
  $check ;     //        dd(auth()->user()->id);
        for($i=0; $i<$count;){

            $user_id=auth()->user()->id;
            $mob=(int)$data[$i];
            $search=User::where('phone',$mob)->first();
            if($search!=null){
            $name=$search['name'];
            $phone=$search['phone'];
                $data['mobile']=$mob;
                $output[$i]=['mobile'=>$mob,
                        'status'=>'true',
                        ];

                $user=User::find($user_id);
                $friend_id=$search['id'];
                
                $check=DB::Table('friend_user')->where('user_id',$user_id)->where('friend_id',$friend_id)->first();
//                if($check=null){
                    
                $user->friends()->attach($friend_id);
//                }
               
               // return response(['status'=>true,'message'=>$search]);
            }else{
                $output[$i]=['mobile'=>$mob,
                        'status'=>'false',
                        ];
                }
            
                    $i++;
                
               // return response(['status'=>false,'message'=>'sorry you have no friends in the app']);
            

        }
//        dd($check);
        
    return response($output);
 }

    
    
    public function friend_list(Request $request){
        $x='dasd';
        dd($x);
       $id=$request->user_id;
        $data=Friend_User::where('user_id',$id)->where('status',1)->pluck('friend_id');
        $count=count($data);
        $user=User::find($id);
        $friends=$user->friends;
        return response(['friends_list'=>$friends]);
    }
}
