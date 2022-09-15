@extends('exment_furniture_management_system::common.base')
@section('title', "ソフトファニチャー棚卸/Soft Furniture Inventory Counting" )
@section('content')
<div>
    <div class="lead text-center"><b>棚卸申請/過去申請一覧/Inventory Counting Records</b></div>
</div>
<div class="box-body table-responsive no-padding">
    <table class="table">
        <tbody>
            @if($unrequested)
            <tr>
                <td style="width: 50%;">
                    <div style="line-height:3rem;"><b>{{ $unrequested->created_at->format('Y年m月d日') }}開始</b></div>
                </td>
                <td style="width: 50%;">
                    <div>
                        <a href="{{ $routeUri }}/request/unrequested-detail" class="btn btn-primary">詳細/Detail</a>
                        <a href="{{ $routeUri }}/request/action" class="btn btn-primary" role="sf-inventory-request" data-date="{{ $unrequested->created_at->format('Y年m月d日') }}">申請/Apply</a>
                        <a href="{{ $routeUri }}/property/{{ $unrequested_new->getValue("Property")->id }}/room/{{ $unrequested_new->getValue("Room_Numbe_inventory")->id }}/result/{{ $unrequested_new->id }}" class="btn btn-primary">続き/continuation</a>
                    </div>
                </td>
            </tr>
            @endif
            @foreach ($requested_date_list as $item)
            <tr>
                <td style="width: 50%;">
                    <div style="line-height:3rem;"><b>{{ \Carbon\Carbon::parse($item)->format('Y年m月d日') }}申請</b></div>
                </td>
                <td style="width: 50%;">
                    <div>
                        <a href="{{ $routeUri }}/request/requested-detail/{{ $item }}" class="btn btn-primary">詳細/Detail</a>
                        <span class="btn btn-default disabled">申請済/Applied</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<form action="{{ $routeUri }}/request/action" method="POST" id="request_form">
    @csrf
    <input type="hidden" name="request_id" value="">
</form>
<script>jQuery(document).ready(c=>{c(document.body).off("click","[role=sf-inventory-request]").on("click","[role=sf-inventory-request]",a=>{a.preventDefault();a=a.target;const d=a.dataset.id;swal({title:a.dataset.date+"\u958b\u59cb\u306e<br>\u68da\u5378\u3092\u7533\u8acb\u3057\u307e\u3059<br>\u672c\u5f53\u306b\u3044\u3044\u3067\u3059\u304b\uff1f",type:"info",showCancelButton:!0,confirmButtonColor:"#3c8dbc",confirmButtonText:"\u306f\u3044",showLoaderOnConfirm:!0,cancelButtonText:"\u3044\u3044\u3048",preConfirm:()=>
{const b=document.getElementById("request_form");b.elements.request_id.value=d;b.submit()}})})});</script>
@endsection