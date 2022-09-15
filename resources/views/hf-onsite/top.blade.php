@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
<div style="width: 70%; margin: 0 auto;">
    @if($draft_id)
    <a href="{{$routeUri}}/property/{{ $property_id }}/room/{{ $room_id }}/result/{{ $hard_furniture_id }}/i/{{ $draft_id }}" class="btn btn-lg btn-light btn-default btn-block">棚卸作業の続き/Continue Inventory Counting</a>
    @else
    <a href="{{$routeUri}}/property" class="btn btn-lg btn-light btn-default btn-block">新規棚卸/Start Inventory Counting</a>
    @endif
    <a href="{{$routeUri}}/request" class="btn btn-lg btn-light btn-default btn-block">棚卸申請/過去棚卸一覧/Inventory Counting Records</a>
    <a href="{{$routeUri}}/merge" class="btn btn-lg btn-light btn-default btn-block">棚卸しデータ成形/理由入力/Inventory data molding / reason input</a>
    <a href="{{$routeUri}}/after-merge" class="btn btn-lg btn-light btn-default btn-block">マージ後データ一覧/List of data after merging</a>
</div>
@endsection