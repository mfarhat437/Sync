<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invitation;
use App\Event;
use App\User;
class invitationController extends Controller
{
    
    
    
    public function send_invitation(Request $request){
        
        $data=new Invitation;
        $data->recipient_id=$request->recipient_id;
        $data->sender_id=$request->sender_id;
        session()->put('recipient_id',$request->recipient_id);
        session()->put('sender_id',$request->sender_id);
        if(session()->has('event_id')){
            
             $data->event_id=session()->get('event_id');
        }else{
        $data->event_id=$request->event_id;
        session()->put('event_id',$request->event_id)  ;  
        }
        $data->save();
        return response(['status'=>true,'message'=>'invitation sent successfully']);
        
    }
    
    
    public function send_invite_notification(){
        $sender_id= session()->get('sender_id');
        $sender_name=User::where('id',$sender_name)->pluck('name');
        $event_id=session()->get('event_id');
        $event_name=Event::where('id',$event_id)->pluck('name');
        $event_date=Event::where('id',$event_id)->pluck('event_date');
        $event_time=Event::where('id',$event_id)->pluck('event_time');
        $recipient_id=session()->get('recipient_id');
        
        OneSignal::sendNotificationToUser(
          [
          'inviter_name'=>$sender_name,
          'event_name'=>  $event_name ,  
          'event_date'=>  $event_date ,  
          'event_time'=>  $event_time ,  
          ],
            $recipient_id,
            $url = url('show_event/$event_id'),
            $data = null,
            $buttons = null,
            $schedule = null
        );
        
    }
    
}
