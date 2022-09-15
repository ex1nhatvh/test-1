@extends('exment_furniture_management_system::common.base')
@section('title', $title )
@section('content')
<form class="form-horizontal" action="{{$action_url}}" name="hf_registry_save" method="post" accept-charset="UTF-8">
    @csrf
    <div class="fields-group">
    @foreach($hard_furniture['value'] as $key => $item)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="string_length" class="col-md-3 col-sm-3 col-sm-offset-0 col-xs-6 col-xs-offset-3 col-sm-pull-left col-xs-pull-left control-label">{{ $item['name'] }}</label>
                <div class="col-md-6 col-sm-6 col-sm-offset-0 col-xs-6 col-xs-offset-3">
                    <input style="width: 100%; text-align: center;" type="text" id="string_length" name="{{ $key }}" value="0" class="form-control value_number options_string_length disableNumberFormat" placeholder="" />
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </div>
    <div class="text-center">
        <a class="btn btn-primary" role="back">戻る/Back</a>
        <button type="submit" class="btn btn-primary">保存/Keep</button>
    </div>
</form>
<script>jQuery(document).ready(a=>{a(".value_number:not(.initialized)").addClass("initialized").bootstrapNumber({upClass:"success",downClass:"primary",center:!0})});</script>
@endsection