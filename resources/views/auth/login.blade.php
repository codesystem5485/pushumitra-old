<!doctype html>
<html lang="en">
   <head>
      <title>{{env('APP_NAME')}} | {{ __('general.login') }}</title>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
      <meta name="description" content="Lucid Bootstrap 4.1.1 Admin Template">
      <meta name="author" content="WrapTheme, design by: ThemeMakker.com">
      <link rel="icon" href="favicon.ico" type="image/x-icon">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/bootstrap/css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/font-awesome/css/font-awesome.min.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/css/main.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/css/color_skins.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/css/custom.css')}}">
      <link rel="icon" href="{{asset('admin/assets/images/logo.png')}}" type="image/icon type">
      <script>
            var webUrl = '{{url("/pashumitra/")}}';
      </script>
   </head> 
   <body class="theme-cyan">
      <div id="wrapper">
         <div class="vertical-align-wrap">
            <div class="vertical-align-middle auth-main">
               <div class="auth-box">
                  <div class="top">
                     <!-- <img src="{{asset('admin/assets/images/logo.jpg')}}" alt=""> -->
                     <strong class="login-text">{{env('APP_NAME')}}</strong>
                  </div>
                 
                  <div class="card">
                     <div class="header">
                        <p class="lead">{{ __('auth.login_to_your_account') }}</p>
                     </div>
                     
                     <div class="alert alert-warning alert-block" style="display:none">
                           <strong class="warning-msg"></strong>
                     </div>
                     
                     <div class="body">
                     <form class="form-auth-small" method="POST" action="{{ route('auth.login') }}" >
                           @csrf
                           <div class="form-group">
                              <label for="signin-email" class="control-label sr-only">{{ __('general.email_or_mobile_number') }}</label>
                              <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}"  autocomplete="email" autofocus placeholder="{{ __('general.email_or_mobile_number') }}"> 

                              <span id="email_err" class="invalid-feedback" role="alert">
                                 <strong>{{ __('messages.email_or_mobile_required') }}</strong>
                              </span>
                              
                           </div>
                           <div class="form-group">
                              <label for="signin-email" class="control-label sr-only">{{ __('general.password') }}</label>
                              <input type="password" class="form-control phone" id="password" name="password" value="{{ old('password') }}"  autocomplete="password" autofocus placeholder="{{ __('general.password') }}"> 
                              <span id="password_err" class="invalid-feedback" role="alert">
                                       <strong>{{ __('messages.password_required') }}</strong>
                              </span>
                           </div>
                           <button type="submit" class="btn btn-primary btn-lg btn-block">  {{ __('general.login_label') }}</button>
                     </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>  
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
      <script src="{{asset('admin/assets/js/login.js')}}"></script>  
      
   </body>
</html>