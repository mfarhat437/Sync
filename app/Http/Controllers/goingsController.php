<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Going;
use App\Event;
use Illuminate\Support\Facades\DB;
use App\User;
class goingsController extends Controller
{



    public function make_going (Request $request){
        $event_id=$request->event_id;
        $check=[];
        $event_max_num=Event::where('id',$event_id)->first();
        // count goings to this event...
        $count=Event::where('id',$event_id)->first(); 
        // $count=$count->goings->count();
        if($event_max_num['max_number']>=$count['going_count']){
            $user_id=auth()->user()->id ;
            $event_id=$request->event_id ;
            $check=Going::where('user_id', $user_id)->where('event_id', $event_id)->first();
            if(!empty($check)){
                return response(['status'=>false,'message'=>'you are aleardy in !' ]);     
            }else{
                $going_data=new Going();
                $going_data->event_id=$request->event_id;
                $going_data->user_id=auth()->user()->id ;
                DB::table('events')->where('id',$request->event_id)->update(['going_count'=>DB::raw('going_count+1')]);    
                $going_data->save();
                return response(['status'=>true,'message'=>'you have joined this activity successfully']);   
            }

        }else{
            return response(['status'=>false,'message'=>'sorry..there is no place in this event']);
        }
    }



    public function user_going_count(Request $request){


        $user=User::find($request->user_id)->goings()->get();
        $goings_count=count($user);
        if(!empty($user)){
            return response(['status'=>true,'no_of_goings'=>$goings_count]);

        }else{
            return response(['status'=>false,'message'=>'you did not sync to any activity']);

        }    
    }

    public function most_sync_users(){
        $goings_count=[];
        $users_id=User::all()->pluck('id');
        //        dd($users_id);
        $count=count($users_id);

        //        foreach($users_id as $key=>$value){
        //            
        //         $goings=User::find($id)->goings()->get();
        //
        //            
        //        }
        for($i=0;$i<$count;){
            $id=$users_id[$i];    
            //        dd($id);
            $goings=User::find($id)->goings()->get();
            $goings_count[$i]=count($goings);   
            $name=User::where('id',$id)->pluck('name'); 



            $i++;
        }
                rsort($goings_count);
        //        foreach($goings_count as $count){
        //            
        //            User::where(count())
        //        }
        return response($goings_count);
    }

}
