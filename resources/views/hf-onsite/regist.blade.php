@extends('exment_furniture_management_system::common.base')
@section('title', $title )
@section('content')
<div>
    <div class="lead text-center"><b>{{ array_get($property, "value.PropertyColumn") }}<br>{{ array_get($room, "value.HardFurniture_Room_Numbe") }}</b></div>
</div>
<div>
    <div class="lead text-center">部屋に置かれている個数を入力してください/Please enter QTY in this room</div>
</div>
{{ Form::model($hard_furniture, ['route'=>['exment.plugins.'.$plugin->id.'.post.hardFurnitureOnsiteSave', $property->id, $room->id, $hard_furniture->id], 'class' => 'form-horizontal']) }}
    <div class="fields-group">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('HardFurniture_hard', 'Desk', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
                    <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                            {{ Form::number('HardFurniture_hard', 0, ['class' => 'form-control value_number', 'data-column_type' => 'integer']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('HardFurniture_Chair', 'Chair', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
                    <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                            {{ Form::number('HardFurniture_Chair', 0, ['class' => 'form-control value_number', 'data-column_type' => 'integer']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('HardFurniture_Peds', 'Peds', ['class' => 'col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label']) }}
                    <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                            {{ Form::number('HardFurniture_Peds', 0, ['class' => 'form-control value_number', 'data-column_type' => 'integer']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="text-center">
                    {{ Form::submit("登録/Regist", ['class'=>'btn btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
@if(false)
<form class="form-horizontal" action="{{$action_url}}" name="hf_registry_save" method="post" accept-charset="UTF-8">
    @csrf
    <div class="fields-group">
    @foreach($hard_furniture['value'] as $key => $item)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="string_length" class="col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label">{{ $item['name'] }}</label>
                <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                    <input style="width: 100%; text-align: center;" type="text" id="string_length" name="{{ $key }}" value="{{ $item['count'] }}" class="form-control value_number options_string_length disableNumberFormat" placeholder="" />
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">登録/Regist</button>
    </div>
</form>
@endif
<script>jQuery(document).ready(a=>{a(".value_number:not(.initialized)").addClass("initialized").bootstrapNumber({upClass:"success",downClass:"primary",center:!0})});</script>
@endsection