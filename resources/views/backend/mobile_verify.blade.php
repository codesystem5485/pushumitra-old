<div class="form verify-mobile-otp">
    <div class="body">
    <span class="error-msg"></span>
    <span class="success-msg"></span>
    <h6>{{ __('general.mobile_number') }}</h6>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <input type="text" name="mobile_number" class="form-control phone" value="{{$mobile_number}}"  placeholder="{{ __('general.mobile_number') }}" disabled>
            </div>
            <div class="form-group">
                <input type="text" name="otp" class="form-control otp" value=""  placeholder="{{ __('general.otp') }}">
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary verify"> {{ __('general.verify') }} </button>
    </div>
</div>