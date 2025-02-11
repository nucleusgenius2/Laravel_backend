
@extends('layout.mail')
@section('content')
    <tr>
        <td>
            <div style="margin-bottom:10px"> {{ __('Ваш код для восстановления пароля') }}</div>
        </td>
    </tr>

    <tr>
        <td>
            {{-- код восстановления пароля --}}
            <span><b>{{ $code }}</b></span>
        </td>
    </tr>


    <tr>
        <td>
            <div style="margin-bottom:15px; margin-top:30px"> {{ __('Если вы не отправляли запрос на смену пароля, то проигнорируйте данное письмо.') }}</div>
        </td>
    </tr>


@endsection
