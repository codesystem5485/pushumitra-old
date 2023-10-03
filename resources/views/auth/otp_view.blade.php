<div class="card">
    <div class="header">
    <p class="lead">Verify OTP</p>
    </div>
    <div class="alert alert-warning alert-block" style="display:none">
        <strong class="warning-msg"></strong>
    </div>
<form class="form-otp" method="POST" action="{{ route('auth.otp') }}" >
    @csrf
    <div class="form-group">
        <label for="signin-email" class="control-label sr-only">{{ __('VERIFY OTP') }}</label>
        <input type="text" class="form-control" id="signin-email" name="otp" value="{{ old('Otp') }}"required autocomplete="phone_number" autofocus placeholder="Enter Otp Here"> 
        @error('otp')  
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror 
        <input type="hidden" name="mobile" value="{{$mobile}}">
        <div class="timer"><B>{{ __('TIME REMAINING') }} : <span id="timer"></B></span></div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg btn-block verify">  {{ __('Verify') }}</button>
    <button type="button" class="btn btn-primary btn-lg btn-block resend" style="display:none">  {{ __('Resend Otp') }}</button>
</form>
</div>
<script>  
    let timerOn = true;
    function timer(remaining) {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            document.getElementById('timer').innerHTML = m + ':' + s;
            remaining -= 1;
            
            if(remaining >= 0 && timerOn) {
                setTimeout(function() {
                    timer(remaining);
                }, 1000);
                return;
            }

            if(!timerOn) {
                // Do validate stuff here
                return;
            }
            $('.resend').show();
            $('.timer').hide();
            $(".verify").prop('disabled', false);
            // Do timeout stuff here
    }
    $(document).ready(function(){
        expMin = "{{\config('constants.otp_expiration_min')}}";
        setTimeout(function(){timer(expMin*60)}, 1000);
        var webUrl = "{{url('/')}}";
    });
        
    $(".form-otp").submit(function(e) { 
        $(".verify").prop('disabled', true);
        e.preventDefault();
        var actionurl = e.currentTarget.action;
        $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'application/json',
                data: $(".form-otp").serialize(),
                dataType:'JSON',
                success: function(res) {
                    $(".verify").prop('disabled', false);
                    var webUrl = "{{url('/')}}";
                    console.log(res.statusCode);
                    if(res.statusCode == 400){
                        $('.alert-warning').show();
                        $('.warning-msg').html(res.message);
                        $('.alert-warning').fadeOut(4000);
                    }if(res.statusCode == 200){
                        href = webUrl+"/dashboard";
                        window.location.replace(href);
                    }
                }
        });
    });
    $(".resend").on('click',function(e){
        e.preventDefault();
        $.ajax({
                url: "{{url('/auth/resend-otp')}}",
                type: 'post',
                dataType: 'application/json',
                data: $(".form-otp").serialize(),
                dataType:'JSON',
                success: function(res) {
                    $(".verify").prop('disabled', false);
                    if(res.statusCode == 200){
                        $('.resend').hide();
                        $('.timer').show();
                        timer(expMin*60);
                    }
                    $('.alert-warning').show();
                    $('.warning-msg').html(res.message);
                    $('.alert-warning').fadeOut(4000);
                    
                }
        });
    });
</script>