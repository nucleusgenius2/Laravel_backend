
@extends('layout.mail')
@section('content')
    <tr>
        <td>
            <div style="margin-bottom:10px"> {{ __('Ваш код подтверждения почты') }}</div>
        </td>
    </tr>

    <tr>
        <td>
            {{-- код подтверждения почты --}}
            <span><b>{{ $code }}</b></span>
        </td>
    </tr>


    <tr>
        <td>
            <div style="margin-bottom:15px; margin-top:30px"> {{ __('Если вы не отправляли запрос на подтверждение почты, то проигнорируйте данное письмо.') }}</div>
        </td>
    </tr>


@endsection
