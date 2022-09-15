@extends('exment_furniture_management_system::request.common')
@section('request-content')
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Room_Number_Sale]', '売却品部屋エリア/Items for sale (Room/Area)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::select(
                    'value[Room_Number_Sale]',
                    $room_option,
                    array_get($request, "value.Room_Number_Sale"), 
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
            {{ Form::label('value[Floor_Name_Sale]', '売却品フロア/Items for sale (Floor)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::text(
                    'value[Floor_Name_Sale]',
                    array_get($request, "value.Floor_Name_Sale"), 
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

<!--<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Preferred_date_Sale]', '実施希望日（以降）/Preferred Schedule', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Preferred_date_Sale]',
                        array_get($request, "value.Preferred_date_Sale"), 
                        [
                            "class"=>"form-control value_date",
                            "data-column_type"=>"date",
                            "style"=>"width: 110px",
                            "autocomplete"=>"off"
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Preferred_date_Sale2]', '実施希望日（以前）/Preferred Schedule', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Preferred_date_Sale2]',
                        array_get($request, "value.Preferred_date_Sale2"), 
                        [
                            "class"=>"form-control value_date",
                            "data-column_type"=>"date",
                            "style"=>"width: 110px",
                            "autocomplete"=>"off"
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Preferred_time_Sale]', '希望開始時間帯/Time Zone', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::select(
                    'value[Preferred_time_Sale]',
                    $time_option,
                    array_get($request, "value.Preferred_time_Sale"), 
                    [
                        "class"=>"form-control",
                        "style"=>"width: 100%;",
                        
                        "data-column_type"=>"select_table",
                        "data-value"=>""
                    ]
                ) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Candidate_date_Sale1]', '実施候補日1/Proposed date1', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Candidate_date_Sale1]',
                        array_get($request, "value.Candidate_date_Sale1"), 
                        [
                            "class"=>"form-control value_date",
                            "data-column_type"=>"date",
                            "style"=>"width: 110px",
                            "autocomplete"=>"off"
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Candidate_date_Sale2]', '実施候補日2/Proposed date2', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Candidate_date_Sale2]',
                        array_get($request, "value.Candidate_date_Sale2"), 
                        [
                            "class"=>"form-control value_date",
                            "data-column_type"=>"date",
                            "style"=>"width: 110px",
                            "autocomplete"=>"off"
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Candidate_date_Sale3]', '実施候補日3/Proposed date3', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Candidate_date_Sale3]',
                        array_get($request, "value.Candidate_date_Sale3"), 
                        [
                            "class"=>"form-control value_date",
                            "data-column_type"=>"date",
                            "style"=>"width: 110px",
                            "autocomplete"=>"off"
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>-->

<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[addfile_drawing_Sale]', '査定書/Assessment', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_drawing_Sale]',
                    [
                        "class"=>"value_file",
                        "data-column_type"=>"file",
                        "id"=>""
                    ]
                )}}
            </div>
        </div>
    </div>
</div>

<!--<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[addfile_operating_Sale]', '指示書（作業）/Instruction Sheet', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_operating_Sale]',
                    [
                        "class"=>"value_file",
                        "data-column_type"=>"file",
                        "id"=>""
                    ]
                )}}
            </div>
        </div>
    </div>
</div>-->

<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[addfile_receipt_Sale]', '受領証/Receipt', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_receipt_Sale]',
                    [
                        "class"=>"value_file",
                        "data-column_type"=>"file",
                        "id"=>""
                    ]
                )}}
            </div>
        </div>
    </div>
</div>

@endsection
@section('request-after-content')

<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Enter_the_amount]', '査定金額/Assessment Price', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8 ">
                <div class="input-group">
                    <span class="input-group-addon">¥</span>
                    {{ Form::number(
                        'value[Enter_the_amount]',
                        array_get($request, "value.Enter_the_amount"),
                        [
                            "class"=>"form-control",
                            'id' => 'value[Enter_the_amount]',
                            'decimal_digit'=>"2",
                            'style'=>"max-width: 200px",
                            'step' => "0.01",
                            'data-column_type' => 'currency',
                        ]
                    )}}
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[memo_Sale]', '売却内容メモ/Memo', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::textarea(
                        'value[memo_Sale]',
                        array_get($request, "value.memo_Sale"), 
                        [
                            "class"=>"form-control",
                            "data-column_type"=>"textarea",
                            "rows" => 6,
                            "cols" => 150
                        ]
                    ) }}
                </div>
            </div>
        </div>
    </div>
</div>-->
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[PONumber_sale]', 'PO#', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                    {{ Form::text(
                        'value[PONumber_sale]',
                        array_get($request, "value.PONumber_sale"), 
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
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[RelatedTickets_sale]', '関連チケット#/RelatedTickets#', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                    {{ Form::text(
                        'value[RelatedTickets_sale]',
                        array_get($request, "value.RelatedTickets_sale"), 
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

<script>jQuery(document).ready(d=>{d('select[name="value[Property]"]').on("select2:select",l=>{console.log(l.params.data.id);d.get(encodeURI("{{ admin_url('') }}/webapi/data/SoftFurnitureDB/query-column?q=PropertyColumn eq "+l.params.data.id)).then(a=>{console.log(a.data);if(a.data){a=a.data;const m=document.createDocumentFragment();for(let h=0;h<a.length;h++){var n=m,w=n.appendChild,e=a[h];const f=document.createElement("div");f.classList.add("row");const k=document.createElement("div");k.classList.add("col-md-2",
"col-sm-2","col-xs-2","text-right");var b=document.createElement("input");b.type="checkbox";b.id="softfurniture-"+e.id;b.name="value[SoftFurniture_Item][]";b.dataset.column_type="checkbox";k.appendChild(b);const g=document.createElement("label");g.classList.add("col-md-8","col-sm-10","col-xs-10");g.setAttribute("for",b.id);b=document.createElement("div");b.classList.add("result-box");const c=document.createElement("div");c.classList.add("result-list");const p=document.createElement("div");p.textContent=
"ID: "+e.id;const q=document.createElement("div");q.textContent="\u62e0\u70b9: "+e.value.PropertyColumn;const r=document.createElement("div");r.textContent="\u90e8\u5c4b\u756a\u53f7: "+e.value.Room_Numbe;const t=document.createElement("div");t.textContent="\u30d5\u30ed\u30a2: "+e.value.Floor_Name;const u=document.createElement("div");u.textContent="SKU: "+e.value.Item_Identifier_number;const v=document.createElement("div");v.textContent="PO: "+e.value.PO_Number;b.appendChild(c);c.appendChild(p);c.appendChild(q);
c.appendChild(r);c.appendChild(t);c.appendChild(u);c.appendChild(v);g.appendChild(b);f.appendChild(k);f.appendChild(g);w.call(n,f)}a=m}else a="";console.log(a);d(".soft_furniture_group").empty().append(a)})});d('select[name="value[Room_Number_move_from]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[PropertyColumn_move_to]"]').not(".admin-added-select2").select2({dropdownParent:null,
allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[Room_Number_Sale]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[organization_name_move_vender]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2")});</script>
@endsection