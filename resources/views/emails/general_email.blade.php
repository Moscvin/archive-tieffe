<html>
<head></head>
<body>
    <div>
        {!! $text !!}
    </div>
    <div>
        <p>Il team di {{$adminOptions['web_application_name']}}</p>
        {!! $adminOptions['email_footer_text'] !!}
        <p>
            <img src="{{URL::to($adminOptions['email_logo_url'])}}" alt="{{$adminOptions['web_application_name']}}">
        </p>
    </div>
</body>
</html>