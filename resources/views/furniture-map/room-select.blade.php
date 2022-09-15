@extends('exment_furniture_management_system::common.base')
@section('title', array_get($property, "value.PropertyColumn") )
@section('content')
<div>
    <div class="lead text-center">フロアを選択/Please select Floor</div>
</div>
<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($floor_room_list as $floor => $room_list)
        <div class="col-md-2 col-sm-3 col-xs-6 selection-box">
            <a role="tab" class="box box-default box-solid text-center" style="display: table;" href="#tab{{ $loop->iteration }}">
                <span style="display: table-cell;vertical-align:middle;">{{ $floor }}</span>
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
            <a class="box box-default box-solid" style="display: table;" href="{{ $next_url }}{{array_get($room, 'id')}}">
                <div class="text-center" style="display: table-cell;vertical-align:middle;">{{array_get($room, 'name')}}
                    @if(array_get($room,"HardFurniture_hard"))
                    <div class="text-center">Desk : {{array_get($room,"HardFurniture_hard")}}</div>
                    <div class="text-center">Chair : {{array_get($room,"HardFurniture_Chair")}}</div>
                    <div class="text-center">Peds : {{array_get($room,"HardFurniture_Peds")}}</div>
                    @endif
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endforeach
@endsection