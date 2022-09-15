@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
<div class="form-horizontal">
    <div class="col-md-12 col-md-offset-0">
        <div class="row">
            <div class="form-group text-center">
                <button class="btn btn-primary select-furniture" data-type="soft">ソフトファニチャー/Soft Furniture</button>
                <button class="btn btn-primary select-furniture" data-type="hard">ハードファニチャー/Hard Furniture</button>
                {{ Form::hidden('route_url', $routeUri) }}
            </div>
        </div>
    </div>
    <div class="property-container" style="display:none;">
        <div>
            <div class="lead text-center">拠点を選択してください。/Please select Location</div>
        </div>
        <div class="col-md-12 col-md-offset-0">
            <div class="row">
                @foreach($properties as $item)
                <div class="col-md-2 col-sm-3 col-xs-6 selection-box">
                    <a class="box box-default box-solid select-property" style="display:table;text-align:center;" href="" data-id="{{array_get($item,'id')}}">
                        <span style="display:table-cell;vertical-align:middle;">{{array_get($item,'value.PropertyColumn')}}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready($=>{
    $(".select-furniture").on('click', e=>{
        $(".select-furniture").removeClass("active");
        const t = $(e.currentTarget);
        t.removeClass("active");
        $(".select-property").each((i,e)=>{
            e.href = $("[name=route_url]").val()+"/"+t.data("type")+"/property/"+e.dataset.id;
        });
        $(".property-container").css("display","block");
    });
});
</script>
@endsection