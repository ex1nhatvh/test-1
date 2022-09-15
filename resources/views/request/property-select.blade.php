@php
$hidden_back = true;    
@endphp
@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
<div>
    <div class="lead text-center">拠点を選択してください。/Please select Location</div>
</div>
{{ Form::open(['url'=>$next_url, 'class' => 'form-horizontal', 'pjax-container']) }}
<div class="col-md-12 col-md-offset-0" role="request-select-property">
    <div class="row">
        @foreach($properties as $item)
        <div class="col-md-2 col-sm-3 col-xs-6 selection-box">
            <a class="box box-default box-solid text-center" role="btn-form-pjax" style="display: table;" href="{{ $next_url }}{{array_get($item,'id')}}"><span style="vertical-align:middle; display:table-cell; text-align:center;">{{array_get($item,'value.PropertyColumn')}}</span></a>
        </div>
        @endforeach
    </div>
</div>
@include('exment_furniture_management_system::request.hidden-form')
{{ Form::close() }}
@endsection