<?php

// SocialAuthFacebookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\User;
use App\FacebookProvider;
use Illuminate\Support\Facades\Hash;


class SocialAuthFacebookController extends Controller
{
  /**
   * Create a redirect method to facebook api.
   *
   * @return void
   */
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
        return redirect('/callback');
    }
    
    

    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function callback()
    {
       $user=Socialite::driver('facebook')->user();
        
       $check=FacebookProvider::where('facebook_id',$user->getId())->first();
        
        if(!$check){
            
            $newUser=new User();
            $newUser->name=$user->getName();
            $newUser->email=$user->getEmail();
            $newUser->image=$user->getAvatar();
            $newUser->password=Hash::make(str_random(8));
            $newUser->password_confirm=Hash::make(str_random(8));
            $newUser->save();
            
            $newprovider=new FacebookProvider();
            $newprovider->facebook_id=$user->getId();
            $newprovider->user_id=$check->id;
            $newprovider->save();
            
        }else{
            
            $newUser=User::find($check->user_id)->first();
            
        }

        auth()->user()->login($newUser);
        return response(['status'=>true,'message'=>'congrats you have logged in successfully !']);
        
    }
    
    
    
    
}
