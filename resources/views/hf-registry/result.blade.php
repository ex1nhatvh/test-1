@extends('exment_furniture_management_system::common.base')
@section('title', array_get($property, "value.PropertyColumn")." ".array_get($room, "value.HardFurniture_Room_Numbe" ))
@section('content')
<div>
    <div class="lead text-center">部屋に置かれている個数を登録しました。/Registered QTY placed in the room.</div>
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
<table class="hf_regist-result">
    <tbody>
        <tr><th>DeskDefective</th><th>ChairDefective</th><th>PedsDefective</th></tr>
        <tr>
            <td>{{array_get($hard_furniture, "value.HardFurniture_Desk_Defective")}}</td>
            <td>{{array_get($hard_furniture, "value.HardFurniture_Chair_Defective")}}</td>
            <td>{{array_get($hard_furniture, "value.HardFurniture_Peds_Defective")}}</td>
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
@endsection