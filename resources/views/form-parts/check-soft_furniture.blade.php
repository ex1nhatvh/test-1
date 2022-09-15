<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            <div class="col-md-2 col-sm-2 col-xs-2 text-right">
                <input type="checkbox" class="value_check" id="value_check_{{ $id }}" name="value_check" data-column_type="checkbox" />
            </div>
            <label class="col-md-8 col-sm-10 col-xs-10" for="value_check_{{ $id }}">
                <div class="result-box in_room">
                    @include('exment_furniture_management_system::parts.soft_furniture-result-box', ['data' => ['id' => 1, '拠点/Location' => 'Ark Hills South', '部屋番号/Room Number' => '1F-101', 'フロア/Floor' => '1', 'SKU' => 'item-222222', 'PO' => '#123456', 'Photo' => 'https://wwj.nereus-stg.com/files/3e7cb360-4ac8-11ec-b20a-ffa8f3e4f6f0.jpg']])
                </div>
            </label>
        </div>
    </div>
</div>