@php
$hidden_back = true;    
@endphp
@extends('exment_furniture_management_system::common.base')
@section('title', array_get($property, "value.PropertyColumn") )
@section('content')
<div>
    <div class="lead text-center">フロアを選択/Please select Floor</div>
</div>
{{ Form::open(['url'=>$next_url, 'class' => 'form-horizontal', 'pjax-container']) }}
<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($floor_room_list as $floor => $room_list)
        <div class="col-md-2 col-sm-3 col-xs-6 selection-box">
            <a role="tab" class="box box-default box-solid text-center" style="display:table;" href="#tab{{ $loop->iteration }}">
                <span style="display:table-cell;vertical-align:middle;">{{ $floor }}</span>
            </a>
        </div>
        @endforeach
    </div>
</div>
<div>
    <div class="lead text-center">部屋・エリアを選択/Please select Room or Area</div>
</div>
@foreach($floor_room_list as $floor => $room_list)
<div id="tab{{$loop->iteration}}" class="col-md-12 col-md-offset-0 tab-body">
    <div class="row">
        @foreach($room_list as $room)
        <div class="col-md-2 col-sm-3 col-xs-6 selection-box">
            <a role="btn-form-pjax" class="box box-default box-solid" style="display:table;" data-property_id="{{ $property_id }}" data-room_id="{{array_get($room, 'id')}}" href="{{ $routeUri }}/{{ $furniture_type }}/{{ $request_type }}">
                <div class="text-center" style="display:table-cell;vertical-align:middle;">{{array_get($room, 'name')}}</div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endforeach
@include('exment_furniture_management_system::request.hidden-form')
{{ Form::close() }}
@endsection