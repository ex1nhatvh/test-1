<div class="result-list">
    <div>ID: {{ array_get($data, "id") }}</div>
    <div>拠点/Location: {{ array_get($data, "拠点") }}</div>
    <div>部屋番号/Room Number: {{ array_get($data, "部屋番号") }}</div>
    <div>フロア/Floor: {{ array_get($data, "フロア") }}</div>
    <div>SKU: {{ array_get($data, "SKU") }}</div>
    <div>PO: {{ array_get($data, "PO") }}</div>
</div>
<div class="text-center result-image">
    <img src="{{ array_get($data, "Photo") }}">
</div>