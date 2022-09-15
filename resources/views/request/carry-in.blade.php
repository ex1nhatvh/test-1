@extends('exment_furniture_management_system::request.common')
@section('request-content')
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[PropertyColumn_Carryin]', '搬入先拠点/Move to (Location)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::select(
                    'value[PropertyColumn_Carryin]',
                    $property_option,
                    array_get($request, "value.PropertyColumn_Carryin"), 
                    [
                        "class"=>"form-control",
                        "style"=>"width: 100%;",
                        "data-target_table_name"=>"PropertiesDB",
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
            <div class="col-md-12 text-center">
                {{ Form::button(
                    '搬入先の拠点・部屋を選択する/Select Location and Room',
                    [
                        "name"=>"select_type",
                        "class"=>"btn btn-primary",
                        "formaction"=>$routeUri."/".$furniture_type."/".$request_type."/property",
                        "formnovalidate",
                        "role"=>"btn-form-pjax",
                        "value"=>"carry_in"
                    ]
                ) }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Room_Numbe_Carryin]', '搬入先部屋エリア/Move to (Area/Room)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::select(
                    'value[Room_Numbe_Carryin]',
                    $room_option,
                    array_get($request, "value.Room_Numbe_Carryin"), 
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
            {{ Form::label('value[Floor_Name_Carryin]', '搬入先フロア/Move to (Floor)', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::select(
                    'value[Floor_Name_Carryin]',
                    $floor_option,
                    array_get($request, "value.Floor_Name_Carryin"), 
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
            {{ Form::label('value[organization_Carryin_vender]', '依頼先/Vendor', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::select(
                    'value[organization_Carryin_vender]',
                    $organization_option,
                    array_get($request, "value.organization_Carryin_vender"), 
                    [
                        "class"=>"form-control",
                        "style"=>"width: 100%;",
                        "data-target_table_name"=>"organization",
                        "data-column_type"=>"select_table",
                        "data-value"=>""
                    ]
                ) }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Preferred_date_Carryin]', '実施希望日（以降）/Preferred Schedule', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Preferred_date_Carryin]',
                        array_get($request, "value.Preferred_date_Carryin"), 
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
            {{ Form::label('value[Preferred_date_Carryin2]', '実施希望日（以前）/Preferred Schedule', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Preferred_date_Carryin2]',
                        array_get($request, "value.Preferred_date_Carryin2"), 
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
            {{ Form::label('value[Preferred_time_Carryin]', '希望開始時間帯/Time Zone', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::select(
                    'value[Preferred_time_Carryin]',
                    $time_option,
                    array_get($request, "value.Preferred_time_Carryin"), 
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
            {{ Form::label('value[Candidate_date_Carryin1]', '実施候補日1/Proposed date1', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Candidate_date_Carryin1]',
                        array_get($request, "value.Candidate_date_Carryin1"), 
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
            {{ Form::label('value[Candidate_date_Carryin2]', '実施候補日2/Proposed date2', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Candidate_date_Carryin2]',
                        array_get($request, "value.Candidate_date_Carryin2"), 
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
            {{ Form::label('value[Candidate_date_Carryin3]', '実施候補日3/Proposed date3', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    {{ Form::text(
                        'value[Candidate_date_Carryin3]',
                        array_get($request, "value.Candidate_date_Carryin3"), 
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
            {{ Form::label('value[addfile_drawing_Carryin]', '指示書（画面）/Instruction Sheet', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_drawing_Carryin]',
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
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[addfile_operating_Carryin]', '指示書（作業）/Instruction Sheet', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_operating_Carryin]',
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

@if($furniture_type == "soft")
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[test]', 'Soft Furniture選択/Select Soft Furniture', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
				
                <div class="input-group">
							{{ Form::number(
								'value[furniture_id_1]',
								array_get($request, "value.furniture_id_1"), 
								[
									"class"=>"form-control",
									"style"=>"width: 100%;",
									"maxlength"=>"256",
									"data-column_type"=>"number",
									"id"=>"furniture_id_1",
									"placeholder"=>"該当IDを入力してください/Please enter the corresponding ID",
								]
							)}}
					<span class="input-group-btn">
						<button id="1" type="button" class="btn btn-sm btn-info searchBtn" style="height:34px;">
							<i class="fa fa-search"></i>&nbsp;サーチ/Search
						</button>
					</span>
				</div>
				<div id="dispError" style="color:red;"></div>
				<div id="result">
					{{ Form::hidden('value[furniture_id_1]', array_get($request, "value.furniture_id_1")) }}
					{{ Form::hidden('value[furniture_id_2]', array_get($request, "value.furniture_id_2")) }}
					{{ Form::hidden('value[furniture_id_3]', array_get($request, "value.furniture_id_3")) }}
					{{ Form::hidden('value[furniture_id_4]', array_get($request, "value.furniture_id_4")) }}
					{{ Form::hidden('value[furniture_id_5]', array_get($request, "value.furniture_id_5")) }}
					{{ Form::hidden('value[furniture_id_6]', array_get($request, "value.furniture_id_6")) }}
					{{ Form::hidden('value[furniture_id_7]', array_get($request, "value.furniture_id_7")) }}
					{{ Form::hidden('value[furniture_id_8]', array_get($request, "value.furniture_id_8")) }}
					{{ Form::hidden('value[furniture_id_9]', array_get($request, "value.furniture_id_9")) }}
					{{ Form::hidden('value[furniture_id_10]', array_get($request, "value.furniture_id_10")) }}
				</div>
                <div>
					<table id="idList" class="table">
						<thead>
							<tr>
								<th style="width:10%" scope="col">ID</th>
								<th style="width:40%" scope="col">SKU:Item Identifier</th>
								<th style="width:40%" scope="col">PO#</th>
								<th style="width:10%" scope="col"> </th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
            </div>
        </div>
    </div>
</div>
@endif

<!--<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[memo_Carryin]', '内容メモ/Memo', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::textarea(
                        'value[memo_Carryin]',
                        array_get($request, "value.memo_Carryin"), 
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
            {{ Form::label('value[Estimate_only]', '見積もりのみ/Only Quotation', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::checkbox(
                        '',
                        null,
                        array_get($request, "value.Estimate_only"),
                        [
                            'class' => "value_switch",
                            'data-column_type' => "yesno",
                            'data-onvalue' => "1",
                            'data-offvalue' => "0",
                        ]
                    ) }}
                    {{ Form::hidden(
                        'value[Estimate_only]',
                        array_get($request, "value.Estimate_only"),
                    ) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[No_workrequest_check]', '作業依頼なし/No work request', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::checkbox(
                        '',
                        null,
                        array_get($request, "value.No_workrequest_check"),
                        [
                            'class' => "value_switch",
                            'data-column_type' => "yesno",
                            'data-onvalue' => "1",
                            'data-offvalue' => "0",
                        ]
                    ) }}
                    {{ Form::hidden(
                        'value[No_workrequest_check]',
                        array_get($request, "value.No_workrequest_check"),
                    ) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[Enter_the_amount]', '見積り金額入力/Estimated Price', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8 ">
                <div class="input-group">
                    <span class="input-group-addon">&yen;</span>
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
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[addfile_estimate_carryin]', '見積書添付/Quotation', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_estimate_carryin]',
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
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[PONumber_carryin]', 'PO#', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                    {{ Form::text(
                        'value[PONumber_carryin]',
                        array_get($request, "value.PONumber_carryin"), 
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
            {{ Form::label('value[addfile_po_carryin]', 'PO#添付/Add PO#', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                {{ Form::file(
                    'value[addfile_po_carryin]',
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
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            {{ Form::label('value[RelatedTickets_carryin]', '関連チケット#/RelatedTickets#', ['class' => 'control-label col-md-2'] ) }}
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                    {{ Form::text(
                        'value[RelatedTickets_carryin]',
                        array_get($request, "value.RelatedTickets_carryin"), 
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
<script>
jQuery(document).ready(d=>{d('select[name="value[Property]"]').on("select2:select",l=>{console.log(l.params.data.id);d.get(encodeURI("{{ admin_url('') }}/webapi/data/SoftFurnitureDB/query-column?q=PropertyColumn eq "+l.params.data.id)).then(a=>{console.log(a.data);if(a.data){a=a.data;const m=document.createDocumentFragment();for(let h=0;h<a.length;h++){var n=m,w=n.appendChild,e=a[h];const f=document.createElement("div");f.classList.add("row");const k=document.createElement("div");k.classList.add("col-md-2",
"col-sm-2","col-xs-2","text-right");var b=document.createElement("input");b.type="checkbox";b.id="softfurniture-"+e.id;b.name="value[SoftFurniture_Item][]";b.dataset.column_type="checkbox";k.appendChild(b);const g=document.createElement("label");g.classList.add("col-md-8","col-sm-10","col-xs-10");g.setAttribute("for",b.id);b=document.createElement("div");b.classList.add("result-box");const c=document.createElement("div");c.classList.add("result-list");const p=document.createElement("div");p.textContent=
"ID: "+e.id;const q=document.createElement("div");q.textContent="\u62e0\u70b9: "+e.value.PropertyColumn;const r=document.createElement("div");r.textContent="\u90e8\u5c4b\u756a\u53f7: "+e.value.Room_Numbe;const t=document.createElement("div");t.textContent="\u30d5\u30ed\u30a2: "+e.value.Floor_Name;const u=document.createElement("div");u.textContent="SKU: "+e.value.Item_Identifier_number;const v=document.createElement("div");v.textContent="PO: "+e.value.PO_Number;b.appendChild(c);c.appendChild(p);c.appendChild(q);
c.appendChild(r);c.appendChild(t);c.appendChild(u);c.appendChild(v);g.appendChild(b);f.appendChild(k);f.appendChild(g);w.call(n,f)}a=m}else a="";console.log(a);d(".soft_furniture_group").empty().append(a)})});d('select[name="value[Room_Number_move_from]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[PropertyColumn_move_to]"]').not(".admin-added-select2").select2({dropdownParent:null,
allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[readonly]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");d('select[name="value[organization_name_move_vender]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2")});
	

$('.searchBtn').on('click', function() {
	var field = $(this).attr('id');
	var count = $("#idList tbody").children().length;
	var count_index = count + 1;
	const str1 = $('#furniture_id_'+field).val();
	
	if(typeof str1 === "undefined" || str1 === null || str1 == "") {
		$('#dispError').append('※正しいFurniture IDを入力してください。/Please enter the correct Furniture ID.');
		return
	}
	else if(count == 10) {
		$('#dispError').append('※10件まで登録できます。/You can register up to 10 items.');
		return
	}
	console.log(str1);
  $.ajax('https://wwj.nereus-stg.com/plugins/furniture_management_system/test/test/'+str1,
      {
        type: 'get',
        dataType: 'json'
      }
    )
    // 検索成功時にはページに結果を反映
    .done(function(data) {
      // 結果リストをクリア
	  console.log(data);
	  $('#dispError').empty();
	  
	  $('#idList tbody').append('<tr><td>'+str1+'</td><td>'+data.sku+'</td><td>'+data.po+'</td><td><button onclick=\"delBtn(this)\" type=\"button\">削除</button></td></tr>');
	  
	  $('input[name="value[furniture_id_'+count_index+']"]').val(str1);
	  
	  console.log($('input[name="value[furniture_id_'+count_index+']"]').val());
	  
	  $('#furniture_id_'+field).val('');
      
    })
    // 検索失敗時には、その旨をダイアログ表示
    .fail(function() {
      $('#dispError').append('※正しいFurniture IDを入力してください。/Please enter the correct Furniture ID.');
    });
});
function delBtn(obj) {
	tr = obj.parentNode.parentNode;
    tr.parentNode.deleteRow(tr.sectionRowIndex);
	
	$('input[name="value[furniture_id_1]"]').val('');
	$('input[name="value[furniture_id_2]"]').val('');
	$('input[name="value[furniture_id_3]"]').val('');
	$('input[name="value[furniture_id_4]"]').val('');
	$('input[name="value[furniture_id_5]"]').val('');
	$('input[name="value[furniture_id_6]"]').val('');
	$('input[name="value[furniture_id_7]"]').val('');
	$('input[name="value[furniture_id_8]"]').val('');
	$('input[name="value[furniture_id_9]"]').val('');
	$('input[name="value[furniture_id_10]"]').val('');
	
	$('#idList tbody tr').each(function(index , elm){
		
		var value = $(elm).find('td').first().text();
		var field = index + 1;
		$('input[name="value[furniture_id_'+field+']"]').val(value);
		console.log($(elm).find('td').first().text());
		
	});
}
</script>
@endsection