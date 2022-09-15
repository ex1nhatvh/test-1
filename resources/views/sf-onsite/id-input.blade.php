@extends('exment_furniture_management_system::common.base')
@section('title', $title )
@section('content')
<div>
    <div class="lead"><b>対象となるソフトファニチャーのIDを入力してください/Please enter the ID of the target soft furniture</b></div>
</div>
<div class="form-horizontal">
    <div class="fields-group">
        @for($i = 0; $i < 5; $i++)
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="form-group">
                    <div class="col-md-12">
                        {{ Form::number(
                            'input_ids[]',
                            null,
                            [
                                "class"=>"form-control",
                                "placeholder"=>"",
                                "maxlength"=>"256",
                                "data-column_type"=>"text"
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
<div>
    <div class="text-center" style="margin-bottom:1em;">
        <a href="{{ $confirm_link??"" }}" class="btn btn-lg btn-primary" id="done_input">判定結果を確認する/Check the judgment result</a>
    </div>
</div>
@endsection