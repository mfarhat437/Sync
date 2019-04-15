<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Event;
use App\Going;
use App\User;
class taskscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *9
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $event_tasks=Event::find($id);
$event_task=$event_tasks->tasks;
        if(empty($event_tasks)){
            return response(['message'=>'no tasks for this event']);
        }else{
        return response($event_task);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $check1=Event::where('user_id',auth()->user()->id)->first();
        $check2=Going::where('event_id',$request->event_id)->where('user_id',auth()->user()->id)->first();
        if($check1!=null or $check2!=null){
            
        $task=new Task;
        $task->task_name=$request->task_name;
        $task->task_creater=auth()->user()->id;
        $task->event_id=$request->event_id;

        $task->save();
        return response(['status'=>true,'message'=>'task created successfully !']);
        }else{
        return response(['status'=>false,'message'=>'sorry you are not allowed to create a task because you ar not going or you are not the event creater !']);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task=Task::where('id',$id)->first();
        return response(['task'=> $task]);
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
        $data=Task::where('id',$id)->first();
        $check=$data['task_creater'];
     $check2=Event::where('id',$data['event_id'])->first();
        if($check!=auth()->user()->id or $check2-['user_id']!=auth()->user()->id){
            
            return response (['status'=>false,'message'=>'sorry you cant edit this task..you are not the event creater or the task creater']);
        }else{
        
            Task::where('id',$id)->update([
         
            'task_name'=>$request->task_name,
     ]) ;
        
            
            return response(['status'=>true,'message'=>'task updated successfully']);

            
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
        
        $task=Task::find($id);
             $check2=Event::where('id',$task['event_id'])->pluck('user_id');

        if($task['task_creater']!=auth()->user()->id or $check2!=auth()->user()->id ){
            return response(['status'=>false,'message'=>'sorry you are not the event creater or the task creater']);
        }else{
       $task->delete();
        return response(['status'=>true,'message'=>'task deleted successfully !']);
        }
    }
    
    
    public function choose_task(Request $request){
        $check=Task::where('id',$request->task_id)->pluck('status')->first();
        $event_id=Task::where('id',$request->task_id)->pluck('event_id')->first();
        $check2=Going::where('event_id',$event_id)->where('user_id',auth()->user()->id)->first();
        if(empty($check2)){
            return response (['status'=>false,'message'=>'sorry you should sync to the event first to choose this task']);
        }else{
            
        
        if($check==0){
            
        $user=User::find(auth()->user()->id);
        $task=$request->task_id;
        $user->tasks()->attach($task) ;   
            
        Task::where('id',$request->task_id)->update([
            
            'status'=>'1',
            
        ]);
        
        return response(['status'=>true,'message'=>'congrats you have choosen this task']) ;   
        }else{
            
            return response (['status'=>false,'message'=>'sorry this task is aleardy choosen by one']);
            
        }
    }
}
}
