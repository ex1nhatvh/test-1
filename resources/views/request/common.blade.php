@extends('exment_furniture_management_system::common.base')
@section('title', $title )
@section('content')
{{ Form::model($request_model, ['route'=>[$route_name, $furniture_type, $request_type], 'class' => 'form-horizontal', 'pjax-container', 'name'=>'select_type', 'value'=>'common', 'files' => true]) }}
{{ Form::hidden('select_type', null) }}
    <div class="fields-group">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[Title]', 'タイトル/Title', ['class' => 'control-label col-md-2 asterisk'] ) }}
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            {{ Form::text(
                                'value[Title]',
                                array_get($request, "value.Title"), 
                                [
                                    "class"=>"form-control",
                                    "placeholder"=>"Kamiyacho 新規申請",
                                    "maxlength"=>"256",
                                    "data-column_type"=>"text",
                                    "required"
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
                    {{ Form::label('value[Property]', '拠点/Location', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        {{ Form::select(
                            'value[Property]',
                            $property_option,
                            array_get($request, "value.Property"), 
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
                            '拠点・部屋を選択する/Select Location and Room',
                            [
                                "name"=>"select_type",
                                "class"=>"btn btn-primary",
                                "formaction"=>$routeUri."/".$furniture_type."/".$request_type."/property",
                                "formnovalidate",
                                "role"=>"btn-form-pjax",
                                "value"=>"common"
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
		
		@if($request_type != "disposal" && $request_type != "sale")
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[Address]', '住所/Address', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        {{ Form::text(
                            'value[Address]',
                            array_get($request, "value.Address"), 
                            [
                                "class"=>"form-control",
                                "maxlength"=>"256",
                                "data-column_type"=>"text",
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
                    {{ Form::label('value[Conditions]', '搬入出条件/Carry in/out requirement', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        {{ Form::text(
                            'value[Conditions]',
                            array_get($request, "value.Conditions"), 
                            [
                                "class"=>"form-control",
                                "maxlength"=>"256",
                                "data-column_type"=>"text",
                                "readonly"
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[Priority]', '優先度/Priority', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        {{ Form::select(
                            'value[Priority]',
                            $priority_option,
                            array_get($request, "value.Priority"), 
                            [
                                "class"=>"form-control ",
                                "style"=>"width: 100%;",
                                "data-column_type"=>"select_table",
                                "data-value"=>""
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>-->
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[Manager]', '担当者/Manager', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        {{ Form::select(
                            'value[Manager]',
                            $user_option,
                            array_get($request, "value.Manager"), 
                            [
                                "class"=>"form-control ",
                                "style"=>"width: 100%;",
                                "data-column_type"=>"select_table",
                                "data-value"=>""
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
		@endif
		
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[FieldPersonnel]', '現場担当者/Field Personnel', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        {{ Form::select(
                            'value[FieldPersonnel]',
                            $user_option,
                            array_get($request, "value.FieldPersonnel"), 
                            [
                                "class"=>"form-control ",
                                "style"=>"width: 100%;",
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
					{{ Form::label('sendMail_user[]', '通知ユーザー/Notification User', ['class' => 'control-label col-md-2'] ) }}
					<div class="col-md-8">
						{{ Form::select(
							'sendMail_user[]',
							$noti_user_option,
							array_get($request, "value.sendMail_user"), 
							[
								"class"=>"form-control ",
								"style"=>"width: 100%;",
								"data-column_type"=>"select_tables",
								"data-value"=>"",
								"multiple"=>"multiple"
							]
						) }}
					</div>
				</div>
			</div>
        </div>
		@if($request_type != "disposal" && $request_type != "sale")
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[FieldPersonnelTel]', '担当者TEL/Manager TEL', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            {{ Form::text(
                                'value[FieldPersonnelTel]',
                                array_get($request, "value.FieldPersonnelTel"), 
                                [
                                    "class"=>"form-control",
                                    "maxlength"=>"256",
                                    "data-column_type"=>"text",
                                    "placeholder"=>"090-1234-5678",
                                ]
                            ) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
		@endif
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('value[Comment]', 'コメント/Comment', ['class' => 'control-label col-md-2'] ) }}
                    <div class="col-md-8">
                        <div class="input-group">
                            {{ Form::textarea(
                                'value[Comment]',
                                array_get($request, "value.Comment"), 
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
        </div>
        @yield('request-content')
@if($furniture_type == "hard")
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            <label class="col-md-2 control-label">ハードファニチャー/Hard Furniture</label>
        </div>
    </div>
</div>
@if($hard_furniture)
    @if($request_type != "change-classification")
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('value[Desk]', 'Desk', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
            <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                {{ Form::number(
                    'value[Desk]',
                    array_get($request, 'value.Desk')>0?array_get($request, 'value.Desk'):0,
                    ['class' => 'form-control value_number', 'data-column_type' => 'integer', 'min' => '0'])
                }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('value[Chair]', 'Chair', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
            <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                {{ Form::number(
                    'value[Chair]',
                    array_get($request, 'value.Chair')>0?array_get($request, 'value.Chair'):0,
                    ['class' => 'form-control value_number', 'data-column_type' => 'integer', 'min' => '0'])
                }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('value[Peds]', 'Peds', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
            <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                {{ Form::number(
                    'value[Peds]',
                    array_get($request, 'value.Peds')>0?array_get($request, 'value.Peds'):0,
                    ['class' => 'form-control value_number', 'data-column_type' => 'integer', 'min' => '0'])
                }}
            </div>
        </div>
    </div>
</div>
    @endif
    @if($request_type != "carry-in")
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('value[DeskDefective]', 'DeskDefective', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
            <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                {{ Form::number(
                    'value[DeskDefective]',
                    array_get($request, 'value.DeskDefective')>0?array_get($request, 'value.DeskDefective'):0,
                    ['class' => 'form-control value_number', 'data-column_type' => 'integer', 'min' => '0'])
                }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('value[ChairDefective]', 'ChairDefective', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
            <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                {{ Form::number(
                    'value[ChairDefective]',
                    array_get($request, 'value.ChairDefective')>0?array_get($request, 'value.ChairDefective'):0,
                    ['class' => 'form-control value_number', 'data-column_type' => 'integer', 'min' => '0'])
                }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('value[PedsDefective]', 'PedsDefective', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
            <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                {{ Form::number(
                    'value[PedsDefective]',
                    array_get($request, 'value.PedsDefective')>0?array_get($request, 'value.PedsDefective'):0,
                    ['class' => 'form-control value_number', 'data-column_type' => 'integer', 'min' => '0'])
                }}
            </div>
        </div>
    </div>
</div>
    @endif
@else
<div>
    <div class="lead text-center">ハードファニチャーのある拠点・部屋を選択してください/Please select a room with Hard Furniture</div>
</div>
@endif
@elseif($furniture_type == "soft")
		@if($request_type != "carry-in")
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            <label class="col-md-2 control-label">Soft Furniture選択/Select Soft Furniture</label>
        </div>
    </div>
</div>
<div class="soft_furniture_group">
    @if(!$soft_furniture_list)
    <div>
        <div class="lead text-center">ソフトファニチャーのある拠点・部屋を選択してください/Please select a room with Soft Furniture</div>
    </div>
    @endif
    @foreach($soft_furniture_list as $soft_furniture)
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2 text-right">
                    {{ Form::checkbox(
                        'value[SoftFurniture][]',
                        $soft_furniture->id,
                        array_get($request, "value.SoftFurniture")?in_array($soft_furniture->id, array_get($request, "value.SoftFurniture")):false,
                        [
                            'id' => 'value_check_'.$soft_furniture->id,
                            'data-column_type' => 'checkbox',
                        ]
                    ) }}
                </div>
                <label class="col-md-8 col-sm-10 col-xs-10" for="value_check_{{ $soft_furniture->id }}">
                    <div class="result-box in_room">
                        @include('exment_furniture_management_system::parts.soft_furniture-result-box', ['data' => [
                            'id' => $soft_furniture->id,
                            '拠点/Location' => array_get($soft_furniture->getValue("PropertyColumn"), "value.PropertyColumn"),
                            '部屋番号/Room Number' => array_get($soft_furniture->getValue("Room_Numbe"), "value.Room_Numbe"),
                            'フロア/Floor' => array_get($soft_furniture->getValue("Room_Numbe"), "value.Floor_Name"),
                            'SKU' => array_get($soft_furniture->getValue("Item_Identifier_number"), "value.Item_Identifier"),
                            'PO' => $soft_furniture->getValue("PO_Number"),
                            'Photo' => \Exceedone\Exment\Model\File::getUrl($soft_furniture->getValue("Photo_Softfurniture"))
                        ]])
                    </div>
                </label>
            </div>
        </div>
    </div>
    @endforeach
</div>
		@endif
@endif
        @yield('request-after-content')
    @if(false)
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="form-group">
                <div class="text-center">
                    <span class="btn btn-default disabled"><i class="fa fa-plus"></i><br>移動元部屋/エリアを追加/Add Room・Area</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="form-group">
                <div class="text-center">
                    {{ Form::hidden('submit_not_save_workflow', 0) }}
                    {{ Form::submit(
                        '保存/Save',
                        [
                            'class' => "btn btn-primary",
							"data-exment-request",
                            'id' => "data-exment-request3",
                            "disabled",
                            "style" => "margin-bottom: 5px;"
                        ]
                    ) }}
　
                    {{ Form::submit(
                        '申請/Apply',
                        [
                            'class' => "btn btn-primary",
							"data-exment-request",
                            'id' => "data-exment-request2",
                            "disabled",
                            "style" => "margin-bottom: 5px;"
                        ]
                    ) }}
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
<textarea style="display:none;" id="request_dialog_text">{{ $dialog_text }}</textarea>
<script>
jQuery(document).ready(a=>{a('select[name="value[Property]"]').not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2").on("select2:select",b=>{a.get(a("#admin_uri").val()+"/webapi/data/PropertiesDB/"+b.params.data.id).then(c=>{a('[name="value[Address]"]').val(c.value.address??"");a('[name="value[Conditions]"]').val(c.value["Carry-inCarry-outconditions"]??"")})});a('select[name="value[FieldPersonnel]"]').not(".admin-added-select2").select2({dropdownParent:null,
allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2").on("select2:select",b=>{a.get(a("#admin_uri").val()+"/webapi/data/user/"+b.params.data.id).then(c=>{a('[name="value[FieldPersonnelTel]"]').val(c.value.telephonenumber??"")})});a(['select[name="value[Priority]"]','select[name="value[Manager]"]','select[name="sendMail_user[]"]'].toString()).not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");
a(".value_file").each(function(b,c){b={overwriteInitial:!0,initialPreviewAsData:!0,browseLabel:"\u53c2\u7167",cancelLabel:"\u53d6\u6d88",showRemove:!1,showUpload:!1,showCancel:!1,dropZoneEnabled:!0,preferIconicPreview:!0,allowedPreviewTypes:["image"],previewFileIcon:'<i class="fa fa-file"></i>',previewFileIconSettings:{txt:'<i class="fa fa-file text-primary"></i>',xml:'<i class="fa fa-file text-primary"></i>',pdf:'<i class="fa fa-file-pdf-o text-primary"></i>',doc:'<i class="fa fa-file-word-o text-primary"></i>',
docx:'<i class="fa fa-file-word-o text-primary"></i>',docm:'<i class="fa fa-file-word-o text-primary"></i>',xls:'<i class="fa fa-file-excel-o text-success"></i>',xlsx:'<i class="fa fa-file-excel-o text-success"></i>',xlsm:'<i class="fa fa-file-excel-o text-success"></i>',ppt:'<i class="fa fa-file-powerpoint-o text-danger"></i>',pptx:'<i class="fa fa-file-powerpoint-o text-danger"></i>',pptm:'<i class="fa fa-file-powerpoint-o text-danger"></i>',zip:'<i class="fa fa-file-archive-o text-muted"></i>'},
deleteExtraData:{_file_del_:"file",_token:"7pYrvrhQdy6UgNlujnJOKPursUixWsft5vDkQAxs",_method:"PUT"},deleteUrl:"{{ admin_url('') }}/data/test/1/filedelete",fileActionSettings:{showZoom:!1,showDrag:!1},msgPlaceholder:"\u30d5\u30a1\u30a4\u30eb\u3092\u9078\u629e",showPreview:!0,dropZoneTitle:"\u30d5\u30a1\u30a4\u30eb\u3092\u30c9\u30e9\u30c3\u30b0\uff06\u30c9\u30ed\u30c3\u30d7\u3057\u3066\u304f\u3060\u3055\u3044",uploadLabel:"\u30a2\u30c3\u30d7\u30ed\u30fc\u30c9",maxFileSize:40960,maxFileSizeHuman:"40 MB",
maxFileSizeHelp:"\u30a2\u30c3\u30d7\u30ed\u30fc\u30c9\u4e0a\u9650\u30b5\u30a4\u30ba : 40 MB",msgSizeTooLarge:'\u30d5\u30a1\u30a4\u30eb "{name}" (<b>{size} KB</b>) \u306f\u30a2\u30c3\u30d7\u30ed\u30fc\u30c9\u53ef\u80fd\u306a\u30b5\u30a4\u30ba <b>{maxSize} KB</b> \u3092\u8d85\u3048\u3066\u3044\u307e\u3059\u3002',deletedEvent:"Exment.CommonEvent.CallbackExmentAjax(jqXHR.responseJSON);"};b.initialPreviewConfig&&0<b.initialPreviewConfig.length&&(b.initialPreviewConfig[0].caption=a(c).data("initial-caption"),
b.initialPreviewConfig[0].type=a(c).data("initial-type"),b.initialPreviewConfig[0].downloadUrl=a(c).data("initial-download-url"));a(c).fileinput(b)});a(".value_file").on("filebeforedelete",function(){return new Promise(function(b,c){swal({title:"\u672c\u5f53\u306b\u524a\u9664\u3057\u307e\u3059\u304b\uff1f",type:"warning",showCancelButton:!0,confirmButtonColor:"#DD6B55",confirmButtonText:"\u78ba\u8a8d",showLoaderOnConfirm:!0,cancelButtonText:"\u53d6\u6d88",preConfirm:function(){return new Promise(function(d){d(b())})}})})});
a(".value_file").on("filedeleted",function(b,c,d,e){Exment.CommonEvent.CallbackExmentAjax(d.responseJSON)});a(".value_date").parent().datetimepicker({useCurrent:!1,format:"YYYY-MM-DD",locale:"ja",allowInputToggle:!0});a(".value_time").parent().datetimepicker({useCurrent:!1,format:"HH:mm:ss",locale:"ja",allowInputToggle:!0});a(".value_number:not(.initialized)").addClass("initialized").bootstrapNumber({upClass:"success",downClass:"primary",center:!0});a(".value_switch").bootstrapSwitch({size:"small",
onText:"YES",offText:"NO",onColor:"primary",offColor:"default",onSwitchChange:function(b,c){a(b.target).closest(".bootstrap-switch").next().val(c?"1":"0").change()}});a(".value_file").on("filebeforedelete",function(){return new Promise(function(b,c){swal({title:"\u672c\u5f53\u306b\u524a\u9664\u3057\u307e\u3059\u304b\uff1f",type:"warning",showCancelButton:!0,confirmButtonColor:"#DD6B55",confirmButtonText:"\u78ba\u8a8d",showLoaderOnConfirm:!0,cancelButtonText:"\u53d6\u6d88",preConfirm:function(){return new Promise(function(d){d(b())})}})})});

a(document).off("click","#data-exment-request3").on("click","#data-exment-request3",{},b=>{
    $("input[name=submit_not_save_workflow]").val(1);
});

a(document).off("click","[data-exment-request]").on("click","[data-exment-request]",{},b=>{
	b.preventDefault();
	return new Promise(function(c,d){
		swal({
			title:a("#request_dialog_text").val(),
			type:"info",
			showCancelButton:!0,
			confirmButtonColor:"#3c8dbc",
			confirmButtonText:"\u306f\u3044",
			showLoaderOnConfirm:!0,
			cancelButtonText:"\u3044\u3044\u3048",
			preConfirm:function(){
				return new Promise(function(e){e(c())})
			}
		}).then(function(result) {
			
  if(result.value === true) {
	  
	  $('form').submit();
	  
  }
});
	})
	}
);
a("value[Title]").val()&&a("[data-exment-request]").removeAttr("disabled");
a(document).on("change",'[name="value[Title]"]',b=>{b.currentTarget.value?a("[data-exment-request]").removeAttr("disabled"):a("[data-exment-request]").attr("disabled","disabled")})});

if($('input[name="value[Title]"]').val() != '') { 
    $('#data-exment-request2').removeAttr('disabled');
    $('#data-exment-request3').removeAttr('disabled');
}
</script>
@endsection