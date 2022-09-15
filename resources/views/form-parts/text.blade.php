<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            <label for="Floor_Name" class="col-md-2  control-label">{{ $label }}</label>
            <div class="col-md-8 ">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                    <input maxlength="256" data-column_type="text" type="text" id="Floor_Name" name="value[Floor_Name]" value="{{ isset($value)? $value: "" }}" class="form-control class_fefrkhrevxjufcdbvnnc value_Floor_Name" placeholder=""{{ isset($attribute)? " ".$attribute: "" }} />
                </div>
            </div>
        </div>
    </div>
</div>