{!! $box_header !!}
<div>
    <div class="lead text-center"><b>{{ array_get($property, "value.PropertyColumn") }}</b></div>
</div>
<div>
    <div class="lead text-center">フロアを選択/Please select Floor</div>
</div>
<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($floor_room_list as $floor => $room_list)
        <div class="col-md-2 col-sm-3 col-xs-6" style="padding:8px;font-size:16px;"><a role="tab" class="box box-default box-solid" style="display:block;padding:8px;min-height:8rem;" href="#tab{{ $loop->iteration }}">{{ $floor }}</a></div>
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
        <div class="col-md-2 col-sm-3 col-xs-6" style="padding:8px;">
            <a class="box box-default box-solid" style="display:block;padding:8px;min-height:8rem;" href="{{ $routeUri }}/property/{{ $property->id }}/room/{{array_get($room, 'id')}}">
                <div class="text-center">{{array_get($room, 'name')}}</div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endforeach