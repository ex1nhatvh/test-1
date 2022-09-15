<div class="text-center" style="margin-bottom: 8px;">
    <a href="{{ $routeUri }}/property" class="btn btn-default">&lt;&nbsp;拠点/Location</a>
    <a href="{{ $routeUri }}/property/{{ $property->id }}" class="btn btn-default">&lt;&nbsp;フロア/Floor</a>
    <a href="{{ $prev_room_url }}" class="btn btn-default prev-room{{ $prev_room_url?"":" disabled" }}"><i class="fa fa-chevron-left"></i></a>
    <a href="{{ $next_room_url }}" class="btn btn-default next-room{{ $next_room_url?"":" disabled" }}"><i class="fa fa-chevron-right"></i></a>
    <a href="{{ $plugin_top_url }}" class="btn btn-default"><i class="fa fa-home"></i></a>
</div>
<div>
    <div class="lead text-center"><b>ソフトファニチャー棚卸/Soft Furniture Inventory Counting</b></div>
</div>
<div class="form-horizontal">
    <div>
        <div class="lead text-center"><b>{{ array_get($property, "value.PropertyColumn") }}<br>{{ array_get($room, "value.Floor_Name") }} {{ array_get($room, "value.Room_Numbe") }}</b></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-xs-4 col-sm-4 col-md-4">
                    @if($prev_room_url)
                    <a href="{{ $prev_room_url }}" class="btn btn-primary btn-block prev-room">前の部屋/Previous room</a>
                    @else
                    <span class="btn btn-primary btn-block disabled">前の部屋/Previous room</span>
                    @endif
                </div>
                <div class="col-xs-4 col-xs-offset-4 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4">
                    @if($next_room_url)
                    <a href="{{ $next_room_url }}" class="btn btn-primary btn-block next-room">次の部屋/Next room</a>
                    @else
                    <span class="btn btn-primary btn-block disabled">前の部屋/Next room</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-xs-4 col-xs-offset-4 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4">
                    <a href="{{ $return_url }}" class="btn btn-primary btn-block">再棚卸/Redo inventory Counting</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @if($is_fail_exists)
            @each('exment_furniture_management_system::parts.soft_furniture-result', $result_list, 'result')
            @else
            <div class="text-center">
                <b>全ての家具が正常/All furniture is normal</b>
            </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-xs-4 col-sm-4 col-md-4">
                    @if($prev_room_url)
                    <a href="{{ $prev_room_url }}" class="btn btn-primary btn-block prev-room">前の部屋/Previous room</a>
                    @else
                    <span class="btn btn-primary btn-block disabled">前の部屋/Previous room</span>
                    @endif
                </div>
                <div class="col-xs-4 col-xs-offset-4 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4">
                    @if($next_room_url)
                    <a href="{{ $next_room_url }}" class="btn btn-primary btn-block next-room">次の部屋/Next room</a>
                    @else
                    <span class="btn btn-primary btn-block disabled">前の部屋/Next room</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-xs-4 col-xs-offset-4 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4">
                    <a href="{{ $return_url }}" class="btn btn-primary btn-block">再棚卸/Redo inventory Counting</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>jQuery(document).ready(c=>{c(document.body).off("click",".next-room").on("click",".next-room",a=>{a.preventDefault();const b=a.target.href;swal({title:"\u6b21\u306e\u90e8\u5c4b\u306eQR\u30b3\u30fc\u30c9\u3092<br>\u8aad\u307f\u53d6\u308a\u307e\u3059\u304b\uff1f",type:"info",showCancelButton:!0,confirmButtonColor:"#3c8dbc",confirmButtonText:"\u78ba\u8a8d",showLoaderOnConfirm:!0,cancelButtonText:"\u53d6\u6d88",preConfirm:()=>{location.href=b}})}).off("click",".prev-room").on("click",".prev-room",a=>
{a.preventDefault();const b=a.target.href;swal({title:"\u524d\u306e\u90e8\u5c4b\u306eQR\u30b3\u30fc\u30c9\u3092<br>\u8aad\u307f\u53d6\u308a\u307e\u3059\u304b\uff1f",type:"info",showCancelButton:!0,confirmButtonColor:"#3c8dbc",confirmButtonText:"\u78ba\u8a8d",showLoaderOnConfirm:!0,cancelButtonText:"\u53d6\u6d88",preConfirm:()=>{location.href=b}})})});</script>