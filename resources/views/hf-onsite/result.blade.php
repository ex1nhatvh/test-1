<div class="text-center" style="margin-bottom: 8px;">
    <a href="{{ $routeUri }}/property" class="btn btn-default">&lt;&nbsp;拠点/Location</a>
    <a href="{{ $routeUri }}/property/{{ $property->id }}" class="btn btn-default">&lt;&nbsp;フロア/Floor</a>
    <a href="{{ $prev_hard_furniture_url }}" class="btn btn-default{{ $prev_hard_furniture_url?"":" disabled" }}"><i class="fa fa-chevron-left"></i></a>
    <a href="{{ $next_hard_furniture_url }}" class="btn btn-default{{ $next_hard_furniture_url?"":" disabled" }}"><i class="fa fa-chevron-right"></i></a>
    <a href="{{ $plugin_top_url }}" class="btn btn-default"><i class="fa fa-home"></i></a>
</div>
<div>
    <div class="lead text-center"><b>{{ array_get($property, "value.PropertyColumn") }} {{ array_get($room, "value.HardFurniture_Room_Numbe" ) }}</b></div>
</div>
<div>
    <div class="lead text-center">部屋に置かれている個数を登録しました。/Registered QTY placed in the room.</div>
</div>
<div>
    <div class="lead">理論値/Theoretical QTY</div>
</div>
<table class="hf_regist-result">
    <tbody>
        <tr><th>Desk</th><th>Chair</th><th>Peds</th></tr>
        <tr>
            <td>{{array_get($hard_furniture, "value.HardFurniture_hard")}}</td>
            <td>{{array_get($hard_furniture, "value.HardFurniture_Chair")}}</td>
            <td>{{array_get($hard_furniture, "value.HardFurniture_Peds")}}</td>
        </tr>
    </tbody>
</table>
<div>
    <div class="lead">実在庫/Actual QTY</div>
</div>
<table class="hf_regist-result">
    <tbody>
        <tr><th>Desk</th><th>Chair</th><th>Peds</th></tr>
        <tr>
            <td>{{array_get($hard_furniture_inventory, "value.Desk_Inventory")}}</td>
            <td>{{array_get($hard_furniture_inventory, "value.chair_Inventory")}}</td>
            <td>{{array_get($hard_furniture_inventory, "value.Peds_Inventory")}}</td>
        </tr>
    </tbody>
</table>
<div>
    <div class="lead">差分/Difference</div>
</div>
<table class="hf_regist-result">
    <tbody>
        <tr><th>Desk</th><th>Chair</th><th>Peds</th></tr>
        <tr>
            <td>{{array_get($hard_furniture_inventory, "value.Desk_Defective_Inventory")}}</td>
            <td>{{array_get($hard_furniture_inventory, "value.chair_Defective_Inventory")}}</td>
            <td>{{array_get($hard_furniture_inventory, "value.peds_Defective_Inventory")}}</td>
        </tr>
    </tbody>
</table>
<div class="text-center">
    @if($prev_hard_furniture_url)
    <a class="btn btn-primary" href="{{ $prev_hard_furniture_url }}">前の部屋/Previous room</a>
    @else
    <span class="btn btn-default disabled">前の部屋/Previous room</span>
    @endif
    @if($next_hard_furniture_url)
    <a class="btn btn-primary" href="{{ $next_hard_furniture_url }}">次の部屋/Next room</a>
    @else
    <span class="btn btn-default disabled">次の部屋/Next room</span>
    @endif
</div>