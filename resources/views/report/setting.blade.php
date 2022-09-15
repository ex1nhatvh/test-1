@extends('exment_furniture_management_system::common.base')
@section('title', $title )
@section('content')
{{ Form::open(['url'=>$action_url, 'class' => 'form-horizontal', 'pjax-container']) }}
    <div class="fields-group">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('property_ids[]', '拠点/Location', ['class' => 'control-label col-md-2 asterisk'] ) }}
                    <div class="col-md-8">
                        {{ Form::select(
                            'property_ids[]',
                            $property_option,
                            null, 
                            [
                                "class"=>"form-control",
                                "style"=>"width: 100%;",
                                "data-target_table_name"=>"PropertiesDB",
                                "data-column_type"=>"select_table",
                                "data-value"=>"",
                                "multiple"=>"multiple",
                                "required"
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    {{ Form::label('start_date', '開始日/Start Date', ['class' => 'control-label col-md-2 asterisk'] ) }}
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                            {{ Form::text(
                                'start_date',
                                null, 
                                [
                                    "class"=>"form-control value_date",
                                    "data-column_type"=>"date",
                                    "style"=>"width: 110px",
                                    "autocomplete"=>"off",
                                    "required"
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
                    {{ Form::label('end_date', '終了日/End Date', ['class' => 'control-label col-md-2 asterisk'] ) }}
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                            {{ Form::text(
                                'end_date',
                                null, 
                                [
                                    "class"=>"form-control value_date",
                                    "data-column_type"=>"date",
                                    "style"=>"width: 110px",
                                    "autocomplete"=>"off",
                                    "required"
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
                    <div class="col-md-12">
                        <span class="icheck">
                            <label class="radio-inline">
                                {{ Form::radio(
                                    'furniture_type',
                                    'soft',
                                    true,
                                    [
                                        "class"=>"minimal value_furniture_type",
                                        "data-column_type"=>"select_valtext",
                                        "autocomplete"=>"off",
                                        "required"
                                    ]
                                )}}&nbsp;ソフトファニチャー/Soft Furniture&nbsp;&nbsp;
                            </label>
                        </span>
                        <span class="icheck">
                            <label class="radio-inline">
                                {{ Form::radio(
                                    'furniture_type',
                                    'hard',
                                    false,
                                    [
                                        "class"=>"minimal value_furniture_type",
                                        "data-column_type"=>"select_valtext",
                                        "autocomplete"=>"off",
                                        "required"
                                    ]
                                )}}&nbsp;ハードファニチャー/Hard Furniture&nbsp;&nbsp;
                            </label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    <div class="text-center">
                        {{ Form::submit(
                            '作成/Create',
                            [
                                'class' => "btn btn-primary"
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
<script>jQuery(document).ready(a=>{a(['select[name="property_ids[]"]'].toString()).not(".admin-added-select2").select2({dropdownParent:null,allowClear:!0,tags:!1,language:"ja",placeholder:{id:"",text:null}}).addClass("admin-added-select2");a(".value_date").parent().datetimepicker({useCurrent:!1,format:"YYYY-MM-DD",locale:"ja",allowInputToggle:!0});a(".value_furniture_type").iCheck({radioClass:"iradio_minimal-blue"})});</script>
@endsection