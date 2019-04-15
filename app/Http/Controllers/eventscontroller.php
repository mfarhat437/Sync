<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invitation;
use App\Event;
use Validator;
use App\User;
use App\Category_event;
use App\Notifications\SomeoneCreatedEvent;
use Notification;
use OneSignal;
use App\Friend_User;
class eventscontroller extends Controller
{


    private $notify;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all_events=Event::orderby('id','desc')->get();
        return response(['stauts'=>true,'events'=>$all_events]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $event_categories=Category_event::all();
        return response($event_categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //        $send_notification=[];
        $validate=validator::make($request->all(),[

            'name'=>'required',
            //            'place'=>'required',
            //            'description'=>'required',
            //            'event_date'=>'required',
            //            'event_time'=>'required',
            //            'min_number'=>'min:1',

        ]

                                 );

        if($validate->fails()){
            return response(['status'=>false,'message'=>$validate->messages()]);
        }else{
            $data=new Event ;
            $data->name=$request->name;
            $data->place=$request->place;
            $data->description=$request->description;
            $data->event_date=$request->event_date;
            $data->event_time=$request->event_time;
            $data->min_number=$request->min_number;
            $data->max_number=$request->max_number;
            $data->cost=$request->cost;
            $data->invite_status=$request->invite_status;

            if(auth()->check()){
                $data->user_id=auth()->user()->id;
            } else{
                $user_id=$request->user_id;
                $data->user_id=$user_id;

                $event_creater=User::select('name')->where('id', $user_id)->pluck('name');
                //                session()->put('event_creater',$event_creater);

            }
            //            $event_creater=session()->get('event_creater');
            //            session()->put('user_id',$request->user_id);
            $data->save();

        }          
        session()->put('event_id',$data->id);
        //                            dd($data->invite_status);
        //        $k=$data->invite_status==?'hhh': 'noo';
        //        dd($k);
        if($data->invite_status==0){

            $friends_from_contacts=Friend_User::where('user_id',auth()->user()->id)->where('status',1)->pluck('friend_id');
            $friends_from_app=Friend_User::where('friend_id',auth()->user()->id)->where('status',1)->pluck('user_id');
            $count=count($friends_from_contacts);
            $count2=count($friends_from_app);
            $event_creater=User::where('id',auth()->user()->id)->pluck('name')->first();
            //                dd($event_creater);
            //            $count=count($all_friends);
            for($i=0;$i<$count;){
                $user_id=auth()->user()->id;
                //dd($user_id);

                $event_id=session()->get('event_id');
                $check=Invitation::where('sender_id',$user_id)->where('recipient_id',$friends_from_contacts[$i])->where('event_id',session()->get('event_id'))->first();

                //                    dd($check);
                if(empty($check)){
                    $data1=new Invitation;
                    $data1->sender_id=$user_id;
                    $data1->recipient_id=$friends_from_contacts[$i] ;
                    $data1->event_id=$event_id; 
                    $data1->save() ;
                }   

//                OneSignal::sendNotificationUsingTags(
//                    $event_creater.'invite you to sync to activity  '.$data->name.'on'.$data->event_date.'at'.$data->event_time,
//                    array(
//                        ["field" => "id", "relation" => "=", "value" => $friends_from_contacts[$i]]
//
//                    ),
//                    $url = null,
//                    $data = [
//                        'event_id'=>$data->id,
//                    ],
//                    $buttons = null,
//                    $schedule = null
//                );    

                //                
                $i++;
            }




            for($j=0;$j<$count2;){
                $user_id=auth()->user()->id;
                //dd($user_id);

                $event_id=session()->get('event_id');
                $check=Invitation::where('sender_id',$user_id)->where('recipient_id',$friends_from_app[$j])->where('event_id',session()->get('event_id'))->first();

                //                    dd($check);
                if(empty($check)){
                    $data1=new Invitation;
                    $data1->sender_id=$user_id;
                    $data1->recipient_id=$friends_from_app[$j] ;
                    $data1->event_id=$event_id; 
                    $data1->save() ;
                } 

//                OneSignal::sendNotificationUsingTags(
//                    $event_creater.'invite you to sync to activity  '.$data->name.'on'.$data->event_date.'at'.$data->event_time,
//                    array(
//                        ["field" => "id", "relation" => "=", "value" => $friends_from_app[$j]]
//
//                    ),
//                    $url = null,
//                    $data = [
//                        'event_id'=>$data->id,
//                    ],
//                    $buttons = null,
//                    $schedule = null
//                );    


                //                
                $j++;
            }


        }

        if($request->invite_status==1){
            $friends_ids=$request->friends_id;
            $friends=explode(',',$friends_ids);


            //                           dd($friends);
            $count=count($friends);

            //                            dd($count);
            $user_id=auth()->user()->id;

            for($i=0;$i<$count;){

                //                dd((int)$friends[$i]);


                $check=Invitation::where('sender_id',$user_id)->where('recipient_id',(int)$friends[$i])->where('event_id',session()->get('event_id'))->get();
                if(!empty($check)){




                    $data1=new Invitation;
                    $data1->sender_id=$user_id;
                    $data1->recipient_id=(int)$friends[$i] ;
                    $data1->event_id=session()->get('event_id'); 
                    $data1->save() ;

                    //                    $sender=session()->get('event_creater');
                    //                    $friends=$sender->getAllFriendships();

                    //  Notification::send(User::find(1),new SomeoneCreatedEvent());
                }


//                OneSignal::sendNotificationUsingTags(
//                    $event_creater.'invite you to sync to activity  '.$data->name.'on'.$data->event_date.'at'.$data->event_time,
//                    array(
//                        ["field" => "id", "relation" => "=", "value" => (int)$friends[$i]]
//
//                    ),
//                    $url = null,
//                    $data = [
//                        'event_id'=>$data->id,
//                    ],
//                    $buttons = null,
//                    $schedule = null
//                );    

                //                dd($send_notification);





                $i++;


            }

        }

        //            //session()->put('event_id',Event::where('name',$request->name)->id);
        return response(['status'=>true,'message'=>'Event created successfully !']);
        //           // Notification::send($notify_users,newSomeoneCreatedEvent($data));

    }

    //    public function create_event4(Request $request){
    //
    //
    //        $all_friends=User::find(session()->get('user_id'))->friends()->pluck('friend_id');
    //        $count=count($all_friends);
    //
    //        for($i=0;$i<$count;){
    //            $user_id=session()->get('user_id');
    //            $event_id=session()->get('event_id');
    //            $check=Invitation::where('sender_id',session()->get('user_id'))->where('recipient_id',$all_friends[$i])->where('event_id',session()->get('event_id'))->get();
    //            if($check=null){
    //                $data=new Invitation;
    //                $data->sender_id=$user_id;
    //                $data->recipient_id=$all_friends[$i] ;
    //                $data->event_id=$event_id; 
    //                $data->save() ;
    //            }    
    //            $i++;
    //        }
    //        return response(['status'=>true,'message'=>'you have invited all friends successfully']);
    //    }

    public function show_friends(){


        $friends=User::find(auth()->user()->id)->friends()->get();
        if(empty($friends)){

            return response(['status'=>false,'message'=>'you do not have any friends to invite']);

        }else{
            return response(['status'=>true,'friends'=>$friends]);

        }


    }
    //    public function create_event5(Request $request){
    //        $user_id=session()->get('user_id');
    //        $check=Invitation::where('sender_id',session()->get('user_id'))->where('recipient_id',$request->friend_id)->where('event_id',session()->get('event_id'))->get();
    //        if($check=null){
    //
    //            $data=new Invitation;
    //            $data->sender_id=$user_id;
    //            $data->recipient_id=$request->friend_id ;
    //            $data->event_id=session()->get('event_id'); 
    //            $data->save() ;
    //            return response(['status'=>true,'message'=>'invitation sent successfully to this friend']);
    //        }else{
    //            $friend_name= User::where('id',$request->friend_id)->pluck('name');
    //            return response(['status'=>false,'message'=>'you have already invited'.$friend_name]);
    //
    //        }
    //    }


    //    public function create_event1(Request $request){
    //        
    //             $validate=validator::make($request->all(),[
    //            
    //            'name'=>'required',
    //        ]);
    //        
    //        if($validate->fails()){
    //            return response(['status'=>false,'message'=>$validate->messages()]);
    //        }else{
    //            $data=new Event ;
    //            $data->name=$request->name;
    //            $data->user_id=$request->user_id;
    //            $data->save();
    //            $event_id=$data->id;
    //            session()->put('event_id',$event_id);
    //            session()->put('user',$request->user_id);
    //
    //            return response(['status'=>true,'message'=>'creating event step1 is successfully']);
    //    }
    //    }
    //    
    //    public function create_event2(Request $request){
    //        
    //        
    //            if(!empty($request->event_date)){
    //        
    //            Event::where('id',session()->get('event_id'))->update([
    //                
    //               'event_date'=>$request->event_date,
    //                
    //                
    //            ]) ;
    //            return response(['status'=>true,'message'=>'creating event step2 is successfully']);
    //    }
    //    }
    //    
    //    public function create_event3(Request $request){
    //        
    //            if(!empty($request->event_time)){
    //            Event::where('id',session()->get('event_id'))->update([
    //                
    //               'event_time'=>$request->event_time, 
    //                
    //                
    //            ]) ;
    //            return response(['status'=>true,'message'=>'creating event step3 is successfully']);
    //    }
    //    }

    //invite all friends



    public function send_notification(){
        $sender=session()->get('event_creater');
        $friends=$sender->getAllFriendships();

        //  Notification::send(User::find(1),new SomeoneCreatedEvent());
        OneSignal::sendNotificationToUser([
            'eventt_creater'=>sonession()->get('event')[1]['event_creater'],
            'event_name'=>session()->get('event')[0]['event_name'],
            'event_date'=>session()->get('event')[2]['event_date'],
            'event_time'=>session()->get('event')[3]['event_date'],
        ],

                                          1,
                                          $url = null,
                                          $data = null,
                                          $buttons = null,
                                          $schedule = null
                                         );


        return response(['status'=>true,'message'=>'notification sent successfully']);

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event=Event::where('id',$id)->first();
        $count=Event::where('id',$id)->withCount('goings')->pluck('goings_count'); 
        return response(['event'=>$event,'going'=>$count]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event=Event::where('id',$id)->first();
        return response(['event'=>$event]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $check=Event::where('id',$id)->first();
        if($check->user_id!=auth()->user()->id){
            return response(['status'=>false,'message'=>'sorry you are not the event creater']);
        }
        if(!empty($check)){
            $data= Event::where('id',$id);
            if(!empty($request->name)){
                $data->update(['name'=>$request->name]);}
            if(!empty($request->place)){
                $data->update(['place'=>$request->place]);}
            if(!empty($request->description)){
                $data->update(['description'=>$request->description]);}
            if(!empty($request->event_date)){
                $data->update(['event_date'=>$request->event_date]);}
            if(!empty($request->event_time)){
                $data->update(['event_time'=>$request->event_time]);}
            if(!empty($request->min_number)){
                $data->update(['min_number'=>$request->min_number]);}
            if(!empty($request->cost)){
                $data->update(['cost'=>$request->cost]);}

            //            }
            //           ->update([
            //
            //           'name'=>$request->name,
            //            'place'=>$request->place,
            //            'description'=>$request->description,
            //            'event_date'=>$request->event_date,
            //            'event_time'=>$request->event_time,
            //            'min_number'=>$request->min_number,
            //            'max_number'=>$request->max_number,
            //            'cost'=>$request->cost,    
            //        ]) ;


            return response(['status'=>true,'message'=>'event data updated successfully']);
        }else{
            return response(['status'=>false,'message'=>'event id does not exist']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $event=Event::find($id);
        if($event->user_id!=auth()->user()->id){
            return response(['status'=>false,'message'=>'sorry you are not the event creater']);
        }else{


            $event->delete();
            return response(['status'=>true,'message'=>'event deleted successfully !']);
        }
    }
}
