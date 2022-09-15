<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="form-group">
            <label for="PropertyColumn" class="col-md-2  control-label">{{ $label }}</label>
            <div class="col-md-8 ">
                <input type="hidden" name="value[PropertyColumn]"/>
                <select class="form-control" style="width: 100%;" name="value[PropertyColumn]" data-target_table_name="PropertiesDB" data-column_type="select_table" data-value="1">
                    <option value=""></option>
                    @foreach($options as $key => $value)
                    <option value="{{ $key }}" selected>{{ $value }}</option>
                    @endforeach
                </select>
                @if(false)
                <div style="margin:0.2em 0 0.5em;">
                    <button type="button" class="btn btn-sm btn-info" data-widgetmodal_url="{{ admin_url('') }}/admin/data/PropertiesDB?modalframe=1" data-widgetmodal_expand="{&quot;target_column_class&quot;:&quot;class_jyprzrcgqbpesxrxpfqn&quot;,&quot;target_column_id&quot;:72,&quot;target_view_id&quot;:null,&quot;display_table_id&quot;:16,&quot;linkage&quot;:null,&quot;target_column_multiple&quot;:0}" data-widgetmodal_getdata_fieldsgroup="{&quot;selected_items&quot;:&quot;class_jyprzrcgqbpesxrxpfqn&quot;}">
                        <i class="fa fa-search"></i>&nbsp;サーチ/Search
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>