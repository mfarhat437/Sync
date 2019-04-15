@component('mail::message')
Welcome {{$data['data]->name}}

please copy this code and past it in the next page
Code:: {{$token}}
@component('mail::button', ['url' => {{route('verify_code')}}])
Button Text
@endcomponent

Thanks,<br>
SYNC
@endcomponent
