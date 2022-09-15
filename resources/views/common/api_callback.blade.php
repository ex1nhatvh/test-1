@php
use \App\Plugins\FurnitureManagementSystem\Includes\FMSUtil\Facades\FMSUtil; 
@endphp
@extends('exment_furniture_management_system::common.base')
@section('title', "申請用APIの利用設定が完了しました")
@section('content')
<div>5秒後にFurniture Management Systemに戻ります</div>
<script>jQuery(document).ready(a=>{localStorage.setItem("{{ FMSUtil::getTokenKey() }}",JSON.stringify({!! $json_str !!}));setTimeout(location.replace("/plugins/furniture_management_system"),5E3)});</script>
@endsection