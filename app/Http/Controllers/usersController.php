<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use Hash;
use App\Mail\reset_password;
use Carbon\Carbon;
use DB;
use Mail;
use App\Going;
use App\Event;
use App\Suggested_friend;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use JWTAuth;
class usersController extends Controller
{
    use SendsPasswordResetEmails;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
//      public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login']]);
//     }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
//        try {
//            // attempt to verify the credentials and create a token for the user
//            if (! $token = JWTAuth::attempt($credentials)) {
//                return response()->json(['error' => 'invalid_credentials'], 401);
//            }
//        } catch (JWTException $e) {
//            // something went wrong whilst attempting to encode the token
//            return response()->json(['error' => 'could_not_create_token'], 500);
//        }

//         all good so return the token
//        return response()->json(compact('token'));

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user_dettails=User::where('email',request('email'))->first();
        $user_goings=User::find($user_dettails->id)->goings()->pluck('event_id');
        $count=count($user_goings);
//        dd($count);
        $events_ids=[];
        for($i=0;$i<$count;){
            
            $events_ids[$i]=Event::where('id',$user_goings[$i])->where('event_date','>',Carbon::now())->pluck('id')->first();
//            dd($events_ids);
           
            $i++;
        }
        
        //        dd($user_goings);
        $user_dettails['synced_events_ids']=$events_ids;
        $user_dettails['image']="http://mokhtar.eslamnasser.com/api/uploads/images/".$user_dettails['image'];
        return $this->respondWithToken($token,$user_dettails);
    }

    public function synced_events(Request $request){

        $synced_events=User::find(auth()->user()->id)->goings()->pluck('event_id');
            
        
        $count=count($synced_events);
        $data=[];
        for($i=0;$i<$count;){
            $data[$i]=Event::where('id',$synced_events[$i])->pluck('name','id');
            $i++;
        }
        if(!empty($data)){
        return response($data);
        }else{
        return response(['message'=>'you did not sync any event before']);
        }
        
        
        
    }
    public function me()
    {
        return response()->json(auth()->user());
    }


    protected function respondWithToken($token,$user_dettails)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory(),
            'user_details'=>$user_dettails,
        ]);
    }


    //  if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){


    // $user = $request->user();

    // $tokenResult = $user->createToken('Personal Access Token');
    //      
    //    return response(['status'=>true,'message'=>' you logged in successfully','access token'=>$tokenResult]);
    // }else{
    //     return response(['status'=>false,'message'=>' sorry Email or Password is not correct !']);
    //  }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request,User $user)
    {

        $check=User::where('email',$request->email)->first();
        if(!empty($check)){

            return response(['status'=>false,'message'=>'sorry this email is exsit']);

        }

        $check2=User::where('phone',$request->phone)->first();
        if(!empty($check2)){

            return response(['status'=>false,'message'=>'sorry this phone number is exsit']);

        }

        $validate=validator::make($request->all(),[

            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:8',
            'gender'=>'required',
            'password_confirm'=>'required',
            'phone'=>'required',


        ]);


        if($request->password!==$request->password_confirm){

            return response(['status'=>false,'message'=>'sorry the password does not match']);
        }

        if($validate->fails()){
            return response(['status'=>false,'message'=>$validate->messages()]);
        }else{
            $data=new User;
            $data->name=$request->name;
            $data->email=$request->email;
            $data->phone=$request->phone;
            $data->gender=$request->gender;
            $data->password=Hash::make($request->password);
            $data->password_confirm=Hash::make($request->password_confirm);

            if($request->file('image')!=null){
                $file=$request->file('image');
                $filename=time(). '.'.$file->getclientOriginalextension();
                $data->image=$filename;
                $file->move(public_path('uploads/images'),$filename);
            }
            $data->save();
            return response(['status'=>true,'message'=>'you have registered successfully !']);
        }
    }



//
//    public function register_step1(Request $request){
//
//        $validate=validator::make($request->all(),[
//
//            'name'=>'required',
//            'email'=>'required|email',
//            'gender'=>'required',
//            'image'=>'required|image',
//        ]);   
//
//        if($validate->fails()){
//            return response(['status'=>false,'message'=>$validate->messages()]);
//        }else{
//            $data=new User;
//            $data->name=$request->name;
//            $data->email=$request->email;
//            $data->gender=$request->gender;
//
//            $file=$request->file('image');
//            $filename=time(). '.'.$file->getclientOriginalextension();
//            $data->image=$filename;
//            session()->put('user_email',$request->email);
//            $data->save();
//
//            return response(['status'=>true,'message'=>'register first step is successfully !']);
//
//
//
//        }
//        return $file->move(public_path('uploads/images'),$filename);
//
//    }
//
//
//    public function register_step2(Request $request){
//
//        $validate=validator::make($request->all(),[
//
//            'password'=>'required|min:8',
//            'password_confirm'=>'required|min:8',
//
//        ]);   
//
//        if($validate->fails()){
//            return response(['status'=>false,'message'=>$validate->messages()]);
//        }else{
//            $password=$request->passowrd;
//            $password_confirm=$request->passowrd_confirm;
//
//            if($password!==$password_confirm){
//
//                return response(['error'=>'sorry the passowrd does not match']);
//
//            }else{
//
//                $email=session()->get('user_email');
//
//                User::where('email',$email)->update([
//                    'password'=>Hash::make($request->password),
//                    'password_confirm'=>Hash::make($request->password_confirm),
//
//                ]);
//
//                return response(['status'=>true,'message'=>'register second step is successfully !']);
//            }
//        }
//    }
//
//
//
//    public function register_step3(Request $request){
//
//        $validate=validator::make($request->all(),[
//
//            'phone'=>'required',
//
//
//        ]);   
//
//        if($validate->fails()){
//            return response(['status'=>false,'message'=>$validate->messages()]);
//        }else{
//            $email=session()->get('user_email');
//            User::where('email',$email)->update([
//                'phone'=>$request->phone,
//
//            ]);
//
//            return response(['status'=>true,'message'=>'register third step is successfully !']);
//        }
//    }





    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::find($id)->first();
        return response(['user_data'=>$user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id=auth()->user()->id;
        $check=User::where('id',auth()->user()->id)->first();
        if(!empty($check)){
            $data= User::where('id',$id);
            if(!empty($request->name)){
                $data->update(['name'=>$request->name]);}
            if(!empty($request->email)){
                $data->update(['email'=>$request->email]);}
            if(!empty($request->phone)){
                $data->update(['phone'=>$request->phone]);}
            if(!empty($request->file('image'))){

                $file=$request->file('image');
                $filename=time(). '.'.$file->getclientOriginalextension();
                $data->update(['image'=>$filename]);
                $file->move(public_path('uploads/images'),$filename);}
            if(!empty($request->gender)){
                $data->update(['gender'=>$request->gender]);}
            if($request->password!=$request->password_confirm){
                return response(['status'=>false,'message'=>'the password deos not match !']);
            }else{


                if(!empty($request->password)){
                    $data->update(['password'=>Hash::make($request->password)]);}
                if(!empty($request->password_confirm)){
                    $data->update(['password_confirm'=>Hash::make($request->password_confirm)]);}
            }



            return response(['status'=>true,'message'=>'your data updated successfully']);
        }  else{
            return response(['status'=>false,'message'=>'user id does not exist !']);


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
        User::find($id)->delete();
        return response(['status'=>true,'message'=>'user deleted successfully !']);
    }

    public function reset_password(Request $request){

        $user=User::where('email',$request->email)->first();
        $token = $this->broker()->createToken($user);

//        $token=str_random(50);
        if(!empty($user)){
            DB::table('password_resets')->insert([

                'email'=>$user->email,
                'token'=>$token,
                'created_at'=>Carbon::now(),
            ]);

//            Mail::to($user->email)->send(new reset_password(['data'=>$user,'token'=>$token]));
            return response(['status'=>true,'message'=>'email sent successfully','token'=>$token]); 
        }else{

            return response(['status'=>false,'message'=>'sorry incorrect email...try again']);

        }
    }


    public function verify_code(Request $request){


        $check=DB::table('password_resets')->where('token',$request->code)->first();

        if(!empty($check)){

            return response(['status'=>true,'message'=>'you reset password correctly']); 
        }else{

            return response(['status'=>false,'message'=>'sorry the code is incorrect']); 

        }

    }



    public function change_password(Request $request){

        $new_pass=$request->new_password;
        $new_confirm=$request->password_confirm;
        if($new_pass==$new_confirm){

            User::where('id',auth()->user()->id)->update([
                'password'=>Hash::make($new_pass),
                'password_confirm'=>Hash::make($new_confirm),

            ]);

            return response(['status'=>true,'message'=>' password updated successfully']); 

        }else{

            return response(['status'=>false,'message'=>' the password does not match']); 
        }



    }
    public function post_suggested_friends(Request $request){

        $user=[];
        $suggested_friends=User::find($request->user_id)->goings;
        //        dd($suggested_friends);
        $events=$suggested_friends->pluck('event_id');
        $event_count=count($events);
        for($i=0;$i<$event_count;){
            $data=Event::find($events[$i])->goings->pluck('user_id');
            //                dd($data);
            $data_count=count($data);
            for($j=0;$j<$data_count;){
                $id=$data[$j];


                if(!empty($id) and $id!=$request->user_id) {
                    //                dd($request->user_id);
                    $check=DB::table('suggest_user_friends')->where('suggested_id',$id)->where('user_id',$request->user_id)->first();
                    if(empty($check)){

                        $suggest= User::find($request->user_id);
                        $suggest->suggests()->attach($id) ;  

                    }    
                }   

                $j++;    
            }
            $i++;

        }
        $user=User::find($request->user_id)->suggests;
        return response($user);
    }


//    public function list_suggested_friends(Request $request){
//        $suggested=User::find($request->user_id)->suggests;
//        return response ($suggested);
//
//    }
    
    public function suggested_friends(Request $request){
        $suggested_users=[];
        $goings=User::find(auth()->user()->id)->goings;
        $friends=User::find(auth()->user()->id)->friends()->pluck('friend_id');
        $count_friends=count($friends);
        $data=[];
        $events=$goings->pluck('event_id');
        $event_count=count($events);
        for($i=0;$i<$event_count;){
                foreach($friends as $friend_id){
            $data=Event::find($events[$i])->goings->where('user_id','!=',$friend_id)->pluck('user_id');
}
            $count2=count($data);
            for($j=0;$j<$count2;){
                
                $id=$data[$j];
                if($id!=auth()->user()->id AND $id!=$friend_id){
                $suggested_users[$j]=User::where('id',$data[$j])->first();
                }
            $j++;
            }
            $i++;
        }
        if(!empty($suggested_users)){
            
        return response ($suggested_users);
        } else{
            return response(['message'=>'there is no suggested friends until now']);
        }   
    }

    //        $users_id=Event::find(1)->goings->pluck('user_id');
    //        $count=count($users_id);
    //        $suggested=[];
    //        for($i=0;$i<$count;){
    //            $suggested[$i]=user::where('id',$users_id[$i])->first();
    //        return response(['suggested_friends'=>$suggested]);

    //$users=$event['user_id'];
    //$data=$suggested_friends->with('goings')->get();

    //        $suggested_friends=Going::where('event_id',$request->event_id)->first();
    //dd($suggested_friends);
    //  $data=User::where('id','user_id')->get();
    //       return response(['data'=>$suggested_friends]);
    //    }




}
