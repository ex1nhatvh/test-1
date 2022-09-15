{!! $box_header !!}
<div>
    <div class="lead text-center"><b>ソフトファニチャー棚卸/Soft Furniture Inventory Counting</b></div>
</div>
<div>
    <div class="lead text-center">拠点を選択してください。/Please select Location</div>
</div>
<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($properties as $item)
            <div class="col-md-2 col-sm-3 col-xs-6">
                <a class="box box-default box-solid" style="display:block;min-height:8rem;font-size:16px;padding:8px;" href="{{ $routeUri }}/property/{{array_get($item,'id')}}">{{array_get($item,'value.PropertyColumn')}}</a>
            </div>
        @endforeach
    </div>
</div>
<style>
.location-list {
    display:inline-block;
    margin: 0 1.5% 8px;
    padding: 8px;
    width: 22%;
    float:left;
    border:1px solid #000;
    border-radius: 4px;
}
input[type=radio] {
    display: none;
}
input[type=radio] + label:hover {
    color: #FFF;
    background-color: #337ab7;
    border-color: #2e6da4;
}
input[type=radio]:checked + label {
    color: #FFF;
    background-color: #337ab7;
    border-color: #2e6da4;
}
input[type=radio]:checked + label:hover {
    background-color: #204d74;
    border-color: #204d74;
}
</style>