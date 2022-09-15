@extends('exment_furniture_management_system::common.base')
@section('title', $title )
@section('content')
<div>
    <div class="lead"><b>対象となるソフトファニチャーのQRコードを読み取ってください/Please read QR code on Soft Furniture</b></div>
</div>
<div>
    <div class="text-center" style="margin-bottom:1em;">
        <button class="btn btn-lg btn-light btn-default" role="readQR" data-qr_action="{{$qr_action}}">QRコードを読込/Read QR code</button>
    </div>
</div>
<a href="{{ $confirm_link??"" }}" id="confirm_link_source"></a>
<a href="{{ $input_link??"" }}" id="input_link_source"></a>
@endsection