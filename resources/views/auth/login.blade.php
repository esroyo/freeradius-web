@extends('layouts.master')
@section('header')<h2>Log In</h2>@stop
@section('content')
    {!! Form::open(['url' => 'login']) !!}
    <div class="form-group">
        {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
        <div class="form-controls">
            {!! Form::email('email', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
        <div class="form-controls">
            {!! Form::password('password', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('remember', 'Remember me') !!}
        {!! Form::checkbox('remember', null, true) !!}
    </div>
    {!! Form::submit('Log in', ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
@stop
