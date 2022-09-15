@extends('exment_furniture_management_system::common.base')
@section('title', "ソフトファニチャー棚卸/Soft Furniture Inventory Counting" )
@section('content')
<div>
    <div class="lead text-center"><b>{{ array_get($property, "value.PropertyColumn") }}<br>{{ array_get($room, "value.Floor_Name") }} {{ array_get($room, "value.Room_Numbe") }}</b></div>
</div>
<form class="form-horizontal" action="{{$action_url}}" name="hf_registry_save" method="post" accept-charset="UTF-8" pjax-container>
    @csrf
    <div class="fields-group">
        @each('exment_furniture_management_system::parts.soft_furniture-result', $result_list, 'result')
        @if(!$is_fail_exists)
        <div class="col-md-12">
            <div class="form-group">
                <div class="text-center">
                    <b>全ての家具が正常/All furniture is normal</b>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="text-center">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" style="min-width:194px;">この部屋の棚卸を完了する/Finish inventory counting of this room</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="form-horizontal">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="text-center">
                    <a href="{{ $routeUri }}/property/{{ $property_id }}/room/{{ $room_id }}" class="btn btn-primary" style="min-width:194px;">QRコード再読込/Reload QR code</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="text-center">
                    <a href="{{ $add_input_url ?? $routeUri."/property/".$property_id."/room/".$room_id."/qr/".$read_ids }}" class="btn btn-primary" style="min-width:194px;">QRコード追加読込/Read additional QR code</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection