<!doctype html>
<html lang="en">
   <head>
      <title>{{env('APP_NAME')}}</title>
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
   </head> 
   <body class="theme-cyan">
      <div id="wrapper">
         <div class="vertical-align-wrap">
            <div class="vertical-align-middle auth-main">
               <div class="auth-box">
                  <div class="top">
                     <img src="https://www.wrraptheme.com/templates/lucid/html/assets/images/logo-white.svg" alt="Lucid">
                  </div>
                  <div class="card">
                     <div class="header">
                        <p class="lead">Reset password</p>
                     </div>
                     @if ($errors->any())
                     <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>	
                        {{ implode('', $errors->all(':message')) }}
                     </div>
                     @endif
                     <div class="body">
                        @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif
                        <form class="form-auth-small" method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>   
                           <div class="bottom">
                              <span class="helper-text">Know your password? <a href="{{route('login')}}">Login</a></span>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>

</html>