@if($state=='select_property')
{!! $box_header !!}
<div>
    <div class="lead text-center"><b>ハードファニチャー初期現地登録/Hard Furniture Intial Registration</b></div>
</div>
<div>
    <div class="lead text-center">拠点を選択してください。/Please select Location</div>
</div>

<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($properties as $item)
            <div class="col-md-2 col-sm-3 col-xs-6" style="padding:8px;"><a class="box box-default box-solid" style="display:block;padding:8px;min-height:8rem;font-size:16px;" href="?property_id={{array_get($item,'id')}}">{{array_get($item,'value.PropertyColumn')}}</a></div>
        @endforeach
    </div>
</div>
@elseif($state=='select_room')
{!! $box_header !!}
<div>
    <div class="lead text-center"><b>{{$selected_property}}</b></div>
</div>
<div>
    <div class="lead text-center">フロアを選択/Please select Floor</div>
</div>
<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($floor_room_list as $floor => $rooms)
        <div class="col-md-2 col-sm-3 col-xs-6" style="padding:8px;font-size:16px;"><a role="tab" class="box box-default box-solid" style="display:block;padding:8px;min-height:8rem;" href="#tab{{$loop->iteration}}">{{$floor}}</a></div>
        @endforeach
    </div>
</div>
<div>
    <div class="lead text-center">部屋・エリアを選択/Please select Room or Area</div>
</div>
@foreach($floor_room_list as $floor => $rooms)
<div id="tab{{$loop->iteration}}" class="col-md-12 col-md-offset-0 tab-body">
    <div class="row">
        @foreach($rooms as $room)
        <div class="col-md-2 col-sm-3 col-xs-6" style="padding:8px;">
            @if(false)
            <a class="box box-default box-solid" style="display:block;padding:8px;min-height:8rem;font-size:16px;" href="{{array_get($room,'url')}}/edit">
            @endif
            <a class="box box-default box-solid" style="display:block;padding:8px;min-height:8rem;" href="?property_id={{$selected_property_id}}&room_id={{array_get($room, 'id')}}">
                <div class="text-center">{{array_get($room, 'name')}}</div>
                <div class="text-center">Desk : {{array_get($room,"HardFurniture_hard")}}</div>
                <div class="text-center">Chair : {{array_get($room,"HardFurniture_Chair")}}</div>
                <div class="text-center">Sleeve : {{array_get($room,"HardFurniture_Peds")}}</div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endforeach
@elseif($state=='regist_furniture')
    {!! $box_header !!}
    @if(false)
    <div>
        <div class="lead text-center"><b>{{ array_get($hard_furniture_data, "PropertyColumn_hard") }}</b></div>
        <div class="lead text-center"><b>{{ array_get($hard_furniture_data, "HardFurniture_Room_Numbe") }}</b></div>
    </div>
    <div>
        <div class="lead text-center">部屋に置かれている個数を入力してください/Please enter QTY in this room</div>
    </div>
    @endif
    <form class="form-horizontal" action="{{$action_url}}" name="hf_registry_save" method="post" accept-charset="UTF-8">
        <div class="fields-group">
        @foreach($hard_furniture_data['value'] as $key => $item)
        <div class="form-group">
            <label for="string_length" class="col-md-4  control-label">{{ $item['name'] }}</label>
            <div class="col-md-8">
                <div class="input-group">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-number-minus btn-primary btn-number-options_string_length">-</button>
                        </span>
                        <input style="width: 200px; text-align: center;" type="text" id="string_length" name="{{ $key }}" value="{{ $item['count'] }}" class="form-control options_string_length disableNumberFormat initialized" placeholder="">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-number-plus btn-success btn-number-options_string_length">+</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        </div>
        <div class="text-center">
            <a class="btn btn-primary" role="back">戻る/Back</a>
            <a class="btn btn-primary" role="fms-hf_registry-save" href="{{$routeUri}}?property_id={{$selected_property_id}}&room_id={{$selected_room_id}}&mode=save" data-token="{{$token}}">保存/Keep</a>
        </div>
    </form>
@elseif($state=='save')
    {!! $box_header !!}
    <div>
        <div class="lead text-center"><b>{{ array_get($hard_furniture_data, "PropertyColumn_hard") }}</b></div>
        <div class="lead text-center"><b>{{ array_get($hard_furniture_data, "HardFurniture_Room_Numbe") }}</b></div>
    </div>
    <div>
        <div class="lead text-center">部屋に置かれている個数を登録しました。/Registered QTY placed in the room.</div>
    </div>
    <table>
        <tbody>
            <tr><th>Desk</th><th>Chair</th><th>Peds</th></tr>
            <tr>
                <td>{{array_get($hard_furniture_data, "value.HardFurniture_hard.count")}}</td>
                <td>{{array_get($hard_furniture_data, "value.HardFurniture_Chair.count")}}</td>
                <td>{{array_get($hard_furniture_data, "value.HardFurniture_Peds.count")}}</td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr><th>DeskDefective</th><th>ChairDefective</th><th>PedsDefective</th></tr>
            <tr>
                <td>{{array_get($hard_furniture_data, "value.HardFurniture_Desk_Defective.count")}}</td>
                <td>{{array_get($hard_furniture_data, "value.HardFurniture_Chair_Defective.count")}}</td>
                <td>{{array_get($hard_furniture_data, "value.HardFurniture_Peds_Defective.count")}}</td>
            </tr>
        </tbody>
    </table>
    <div class="text-center">
        @if($prev_hard_furniture)
        <a class="btn btn-primary" href="{{$routeUri}}?property_id={{array_get($prev_hard_furniture, "value.PropertyColumn_hard")}}&room_id={{array_get($prev_hard_furniture, "value.Room_Numbe_hard")}}">前の部屋/Previous room</a>
        @else
        <span class="btn btn-default disabled">前の部屋/Previous room</span>
        @endif

        @if($next_hard_furniture)
        <a class="btn btn-primary" href="{{$routeUri}}?property_id={{array_get($next_hard_furniture, "value.PropertyColumn_hard")}}&room_id={{array_get($next_hard_furniture, "value.Room_Numbe_hard")}}">次の部屋/Next room</a>
        @else
        <span class="btn btn-default disabled">次の部屋/Next room</span>
        @endif
    </div>
@endif
<style>
table {
    margin: 0 auto 16px;
}
th, td{
    border: 1px solid #000;
    width: 100px;
}
td {
    height: 60px;
}
</style>
<script>
jQuery(document).ready($=>{
    console.log("ready");
    var $body = $(document.body);
    $body
    .off("click", ".btn-number-plus")
    .off("click", ".btn-number-minus")
    .off("click", "[role=fms-hf_registry-save]")
    .on("click", ".btn-number-plus", e=>{
        var $input = $(e.currentTarget).closest(".input-group").find("input");
        $input.val( parseInt($input.val(),10) + 1 );
    })
    .on("click", ".btn-number-minus", e=>{
        var $input = $(e.currentTarget).closest(".input-group").find("input");
        $input.val( $input.val() - 1 );
    })
    .on("click", "[role=fms-hf_registry-save]", e=>{
        e.preventDefault();
        var jump_url = e.currentTarget.href;
        var token = e.currentTarget.dataset.token;
        var form = document.forms["hf_registry_save"];
        $.ajax({
            type: "PUT",
            url: document.forms["hf_registry_save"].action,
            headers: {
                'X-HTTP-Method-Override': 'PUT',
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            dataType: "json",
            data: JSON.stringify({
                value: {
                    HardFurniture_hard: form.HardFurniture_hard.value,
                    HardFurniture_Chair: form.HardFurniture_Chair.value,
                    HardFurniture_Peds: form.HardFurniture_Peds.value,
                    HardFurniture_Desk_Defective: form.HardFurniture_Desk_Defective.value,
                    HardFurniture_Chair_Defective: form.HardFurniture_Chair_Defective.value,
                    HardFurniture_Peds_Defective: form.HardFurniture_Peds_Defective.value,
                }
            })
        }).then(msg=>{
            location.href = jump_url;
        })
    });
})
</script>