@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
{{ Form::open(['url'=>$action_url, 'class' => 'form-horizontal', 'pjax-container', 'name'=>'select_type', 'value'=>'common', 'files' => true]) }}
{{ Form::hidden('property_id', $property_id) }}
<div class="fields-group">
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
<script>jQuery(document).ready(a=>{a(".value_date").parent().datetimepicker({useCurrent:!1,format:"YYYY-MM-DD",locale:"ja",allowInputToggle:!0})});</script>
@endsection