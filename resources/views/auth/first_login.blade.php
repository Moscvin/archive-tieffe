@extends('layouts.app')

@section('content')
    <!-- Theme style -->

    <div class="container">
        <div class="login-box">
            <div class="login-logo">
                <a href="#" style="color:#FFFFFF"><img src="/img/v_logo.png" alt="logo"></a>
            </div><!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Primo accesso</p>
                @if(Session::has('first_ok_first'))
                    <div class="first_ko_first alert alert-success">
                        {!! Session::get('first_ok_first') !!}
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a href="{{url("login")}}" class="btn btn-primary btn-block btn-flat">Effettua il login</a>
                        </div><!-- /.col -->
                    </div>
                @else
                    <form method="POST" action="{{ route('first-login') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-user"></i></span>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="email">
                            </div>
                            @if ($errors->has('email'))
                                <div class="alert alert-danger">
                                    <li><strong>{{ $errors->first('email') }}</strong></li>
                                </div>
                            @endif
                            @if(Session::has('first_ko'))
                                <div class="first_ko">
                                    {!! Session::get('first_ko') !!}
                                </div>
                            @endif
                            @if(Session::has('first_ok'))
                                <div class="first_ok">
                                    {!! Session::get('first_ok') !!}
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                                    {!! Captcha::display(['data-theme' => 'white']) !!}
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <button id="submit-first" type="submit" class="btn btn-primary btn-block btn-flat" name="save">Invia la password</button>
                            </div><!-- /.col -->
                        </div><!-- row-->
                        <a href="{{ route('login') }}">Login</a><br>
                        <a href="{{ route('retrieve-password') }}" class="text-center">Reimposta la password</a>
                    </form>
                @endif
            </div> <!-- login-box-body -->
        </div> <!-- login - box-->
    </div> <!--container-->

@endsection

@push('scripts')
<script>
    $( document ).ready(function() {

        $("#submit-first").click(function(event){

            var response = grecaptcha.getResponse();

            if(response.length == 0){
                event.preventDefault();
                $('.g-recaptcha > div').addClass('recaptcha-danger');
            } else {
                $('.g-recaptcha > div').removeClass('recaptcha-danger');
            }

        });

    });
</script>
@endpush