@if(array_get($result, "result") != "正常")
<div class="result-container">
    <div class="result-box in_room">
        @if(array_get($result, "in_room"))
            @include('exment_furniture_management_system::parts.soft_furniture-result-box', ['data' => array_get($result, "in_room")])
        @else
            該当なし/Not applicable
        @endif
    </div>
    <div class="result-box from_qr">
        @if(array_get($result, "from_qr"))
            @include('exment_furniture_management_system::parts.soft_furniture-result-box', ['data' => array_get($result, "from_qr")])
        @else
            該当なし/Not applicable
        @endif
    </div>
    <div>
        <div class="result-title">判定結果/judgment result</div><div class="result-text">{{ array_get($result, "result") }}</div>
    </div>
</div>
@endif
@if(array_get($result, "in_room"))
<input type="hidden" name="soft_furniture_ids[]" value="{{ array_get($result, "in_room.id") }}">
@elseif(array_get($result, "from_qr"))
<input type="hidden" name="soft_furniture_ids[]" value="{{ array_get($result, "from_qr.id") }}">
@endif
<input type="hidden" name="soft_furniture_results[]" value="{{ array_get($result, "result") }}">