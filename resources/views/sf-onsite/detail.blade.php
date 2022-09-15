@extends('exment_furniture_management_system::common.base')
@section('title', "ソフトファニチャー棚卸/Soft Furniture Inventory Counting" )
@section('content')
<div>
    <div class="lead text-center"><b>棚卸確認した拠点部屋一覧/List of rooms that have been inventoried</b></div>
</div>
<div class="box-body table-responsive no-padding">
    <table class="table">
        <tbody>
            @foreach ($request_list as $item)
            <tr>
                <td style="width: 25%;">
                    <div style="line-height:3rem;"><b>{{ $item->getValue("Property")? $item->getValue("Property")->getValue("PropertyColumn"): "" }}</b></div>
                </td>
                <td style="width: 25%;">
                    <div style="line-height:3rem;"><b>{{ $item->getValue("Room_Numbe_inventory")? $item->getValue("Room_Numbe_inventory")->getValue("Floor_Name"): "" }}</b></div>
                </td>
                <td style="width: 25%;">
                    <div style="line-height:3rem;"><b>{{ $item->getValue("Room_Numbe_inventory")? $item->getValue("Room_Numbe_inventory")->getValue("Room_Numbe"): "" }}</b></div>
                </td>
                <td style="width: 25%;">
                    <div style="line-height:3rem;">
                        <b>{{ $item->created_at->format('Y/m/d') }}</b>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if(isset($has_unrequested))
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <div class="text-center">
                <a href="{{ $return_url }}" class="btn btn-primary">棚卸の続きを始める/Continue Inventory Counting</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection