@extends('exment_furniture_management_system::request.common')
@section('request-content')
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Room_Number_Change]', '変更元部屋エリア/Change from (Location)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::select(
                    'value[Room_Number_Change]',
                    $room_option,
                    array_get($request, "value.Room_Number_Change"), 
                    [
                        "class"=>"form-control",
                        "style"=>"width: 100%;",
                        "data-target_table_name"=>$furniture_type=="soft"?"LocationRoom":"LocationRoom_hard",
                        "data-column_type"=>"select_table",
                        "data-value"=>"",
                        "readonly"
                    ]
                ) }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Floor_Name_Change]', '変更元部屋フロア/Change from (Room/Area)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::text(
                    'value[Floor_Name_Change]',
                    array_get($request, "value.Floor_Name_Change"), 
                    [
                        "class"=>"form-control",
                        "style"=>"width: 100%;",
                        "maxlength"=>"256",
                        "data-column_type"=>"text",
                        "readonly"
                    ]
                )}}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[RelatedTickets_change]', '関連チケット#/RelatedTickets#', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                    {{ Form::text(
                        'value[RelatedTickets_change]',
                        array_get($request, "value.RelatedTickets_change"), 
                        [
                            "class"=>"form-control",
                            "data-column_type"=>"text",
                            "style"=>"width: 150px",
                            "autocomplete"=>"off"
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('request-after-content')
<script>jQuery(document).ready(d=>{d('select[name="value[Property]"]').on("select2:select",l=>{console.log(l.params.data.id);d.get(encodeURI("{{ admin_url('') }}/webapi/data/SoftFurnitureDB/query-column?q=PropertyColumn eq "+l.params.data.id)).then(b=>{if(b.data){b=b.data;const m=document.createDocumentFragment();for(let h=0;h<b.length;h++){var n=m,w=n.appendChild,e=b[h];const f=document.createElement("div");f.classList.add("row");const k=document.createElement("div");k.classList.add("col-md-2","col-sm-2",
"col-xs-2","text-right");var a=document.createElement("input");a.type="checkbox";a.id="softfurniture-"+e.id;a.name="value[SoftFurniture_Item][]";a.dataset.column_type="checkbox";k.appendChild(a);const g=document.createElement("label");g.classList.add("col-md-8","col-sm-10","col-xs-10");g.setAttribute("for",a.id);a=document.createElement("div");a.classList.add("result-box");const c=document.createElement("div");c.classList.add("result-list");const p=document.createElement("div");p.textContent="ID: "+
e.id;const q=document.createElement("div");q.textContent="\u62e0\u70b9: "+e.value.PropertyColumn;const r=document.createElement("div");r.textContent="\u90e8\u5c4b\u756a\u53f7: "+e.value.Room_Numbe;const t=document.createElement("div");t.textContent="\u30d5\u30ed\u30a2: "+e.value.Floor_Name;const u=document.createElement("div");u.textContent="SKU: "+e.value.Item_Identifier_number;const v=document.createElement("div");v.textContent="PO: "+e.value.PO_Number;a.appendChild(c);c.appendChild(p);c.appendChild(q);
c.appendChild(r);c.appendChild(t);c.appendChild(u);c.appendChild(v);g.appendChild(a);f.appendChild(k);f.appendChild(g);w.call(n,f)}b=m}else b="";d(".soft_furniture_group").empty().append(b)})});d('select[name="value[Room_Number_move_from]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[PropertyColumn_move_to]"]').not(".admin-added-select2").select2({dropdownParent:null,
allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[Room_Number_Change]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[organization_name_move_vender]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2")});</script>
@endsection