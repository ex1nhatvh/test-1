<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            <label for="number" class="col-md-2 control-label">{{ $label ?? "" }}</label>
            <div class="col-md-8">
                <div class="input-group">
                    <input type="number" data-column_type="integer" style="width: 100px" id="number" name="{{ $name ?? "" }}" value="{{ $value ?? 1 }}" class="form-control value_number disableNumberFormat" placeholder="" />
                </div>
            </div>
        </div>
    </div>
</div>