@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
<div>
    <div class="lead text-center">拠点を選択してください。/Please select Location</div>
</div>
<div class="col-md-12 col-md-offset-0">
    <div class="row">
        @foreach($properties as $item)
        <div class="col-md-2 col-sm-3 col-xs-6 selection-box">
            @if(true)
            <a class="box box-default box-solid" style="display:table;text-align:center;" href="{{ $routeUri }}/property/{{array_get($item,'id')}}">
                <span style="display:table-cell;vertical-align:middle;">{{array_get($item,'value.PropertyColumn')}}</span>
            </a>
            @else
            <a class="btn btn-default btn-solid" href="{{ $routeUri }}/property/{{array_get($item,'id')}}">{{array_get($item,'value.PropertyColumn')}}</a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection