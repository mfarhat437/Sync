<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Friend_User;
class friendshipsController extends Controller
{

    public function send_friend_request(Request $request){

        $user=User::where('id',auth()->user()->id)->first();  
        $data=new Friend_User;
        $data->user_id=auth()->user()->id;
        $data->friend_id=$request->recipient_id;    
        $data->status=0;
        $data->save();

        //        $recipient=User::where('id',$request->recipient_id)->first();
        session()->put('sender',$data->user_id);
        //        session()->put('recipient',$request->recipient_id); 
        //        $user->befriend($recipient);
        //        if($user->hasSentFriendRequestTo($recipient)){

        return response(['status'=>true,'message'=>'friend request sent successfully']);
        //        }else{
        //        return response(['status'=>false]);     
        //        }

    }


    public function accept_friend_request(){

        //        $user=session()->get('recipient');
        $sender=session()->get('sender');

        Friend_User::where('user_id',$sender)->where('friend_id',auth()->user()->id)->update([

            'status'=>1,
        ]) ;       
        return response(['status'=>true,'message'=>'congrats you are now friends!']);


    }

    public function deny_friend_request(){

        //        $user=session()->get('recipient');
        $sender=session()->get('sender');

        Friend_User::where('user_id',$sender)->where('friend_id',auth()->user()->id)->update([

            'status'=>2,
        ]) ;       
        return response(['status'=>true,'message'=>'you denied the friend request!']);

    }



    public function friends_list($id){


        $friends=[];
        $friends2=[];
        $data=Friend_User::where('user_id',$id)->where('status',1)->pluck('friend_id');
        $data2=Friend_User::where('friend_id',$id)->where('status',1)->pluck('user_id');
        $count=count($data);
        $count2=count($data2);
        $count3=$count+$count2;
        for($i=0;$i<$count;){

            $info=User::where('id',$data[$i])->first();
            $friends[$i]['name']=$info['name'];
            $friends[$i]['Email']=$info['email'];
            $friends[$i]['image']=url('/uploads/images/').$info['image'];
            $i++;
        }
        for ($k=0;$k<$count2;){
            $info2=User::where('id',$data2[$k])->first();
            $friends2[$k]['name']=$info2['name'];
            $friends2[$k]['Email']=$info2['email'];
            $friends2[$k]['image']=url('/uploads/images/').$info2['image'];
            $k++;
        }

        if(!empty($friends)){
            return response(['friends_from_contacts'=>$friends,'friends_from_app'=>$friends2]);
        }else{
            return response(['friends_list'=>'you do not have friends until now']);
        }


        //        $user=session()->get('recipient');
        //        $list=$user->getFriendRequests();
        //
        //        if(!empty($list)){
        //            
        //        return response(['status'=>true,'list'=>$list]);
        //        }else{
        //            
        //        return response(['status'=>false,'list'=>'there is no friends requests for you']);
        //
        //        }
        //    }


    }
}
