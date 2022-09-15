<div class="text-center" style="margin-bottom: 8px;">
    <a href="{{ $routeUri }}" class="btn btn-default">&lt;&nbsp;拠点/Location</a>
    <a href="{{ $routeUri }}/{{ $furniture_type }}/property/{{ $property->id }}" class="btn btn-default">&lt;&nbsp;フロア/Floor</a>
    <a href="{{ $prev_room_url }}" class="btn btn-default{{ $prev_room_url?"":" disabled" }}"><i class="fa fa-chevron-left"></i></a>
    <a href="{{ $next_room_url }}" class="btn btn-default{{ $next_room_url?"":" disabled" }}"><i class="fa fa-chevron-right"></i></a>
    <a href="{{ $plugin_top_url }}" class="btn btn-default"><i class="fa fa-home"></i></a>
</div>
<div>
    <div class="lead text-center"><b>{{ $furniture_type=="soft"?"ソフト":"ハード" }}ファニチャー家具配置/Furniture Location</b></div>
</div>
<div>
    <div class="lead text-center"><b>
        {{ $property->getValue("PropertyColumn") }}<br>
        @if($furniture_type=="soft")
        {{ $room->getValue("Room_Numbe") }}
        @elseif($furniture_type=="hard")
        {{ $room->getValue("HardFurniture_Room_Numbe") }}
        @endif
    </b></div>
</div>
@if($furniture_type=="soft")
<div class="row">
    <div class="col-xs-offset-1 col-xs-10 col-md-offset-1 col-md-10 col-lg-offset-1 col-lg-10" style="display:table;border-bottom:1px solid #999;width:80%;padding:8px 0 16px;margin-bottom:8px;">
    </div>
</div>
@foreach($soft_furniture_list as $soft_furniture)
<div class="row">
    <div class="col-xs-offset-1 col-xs-10 col-md-offset-1 col-md-10 col-lg-offset-1 col-lg-10" style="display:table;border-bottom:1px solid #999;width:80%;padding:8px 0 16px;margin-bottom:8px;">
        <div class="col-md-4 col-sm-4 col-xs-4" style="float:none; display: table-cell; vertical-align:middle;">
            {{ $soft_furniture->getValue('Item_Identifier_number')?$soft_furniture->getValue('Item_Identifier_number')->getValue('Item_Identifier'):"" }} <br>
            ID : {{ $soft_furniture->id }} <br>
            PO : {{ $soft_furniture->getValue('PO_Number') }}
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3 text-center result-image" style="float:none;">
            <img width="" src="{{ \Exceedone\Exment\Model\File::getUrl($soft_furniture->getValue('Photo_Softfurniture')) }}">
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3" style="float:none;display:table-cell;vertical-align:middle;">
            {{ $soft_furniture->getValue('SoftFurniture_Status') }}
        </div>
    </div>
</div>
@endforeach
@elseif($furniture_type=="hard")
<div class="row">
    <table style="width:80%;margin:0 auto;">
        <tbody>
            <tr style="border-top:1px solid #999;">
                <td style="width:50%;vertical-align:middle;font-size:2rem;height:60px;text-align:center"><b>Desk</b></td><td style="width:50%;vertical-align:middle;font-size:2rem;padding-left:16px;"><b>{{ $hard_furniture->getValue('HardFurniture_hard') }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="row">
    <table style="width:80%;margin:0 auto;">
        <tbody>
            <tr style="border-top:1px solid #999;">
                <td style="width:50%;vertical-align:middle;font-size:2rem;height:60px;text-align:center"><b>Chair</b></td><td style="width:50%;vertical-align:middle;font-size:2rem;padding-left:16px;"><b>{{ $hard_furniture->getValue('HardFurniture_Chair') }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="row">
    <table style="width:80%;margin:0 auto;">
        <tbody>
            <tr style="border-top:1px solid #999;border-bottom:1px solid #999;">
                <td style="width:50%;vertical-align:middle;font-size:2rem;height:60px;text-align:center"><b>Peds</b></td><td style="width:50%;vertical-align:middle;font-size:2rem;padding-left:16px;"><b>{{ $hard_furniture->getValue('HardFurniture_Peds') }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
@endif