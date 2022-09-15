@for($i=0;$i<6;$i++)
<div class="form-group">
    <label for="string_length" class="col-md-2  control-label">Desk</label>
    <div class="col-md-8 ">
        <div class="input-group">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-number-minus btn-primary btn-number-options_string_length">-</button>
                </span>
                <input max="63999" style="width: 100px; text-align: center;" type="text" id="string_length" name="options[string_length]" value="256" class="form-control options_string_length disableNumberFormat initialized" placeholder="">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-number-plus btn-success btn-number-options_string_length">+</button>
                </span>
            </div>
        </div>
    </div>
</div>
@endfor
<script>
jQuery(document).ready($=>{
    console.log("ready");
    var $body = $(document.body);
    $body
    .on("click", ".btn-number-plus", e=>{
        var $input = $(e.currentTarget).closest(".input-group").find("input");
        $input.val( parseInt($input.val(),10) + 1 );
    })
    .on("click", ".btn-number-minus", e=>{
        var $input = $(e.currentTarget).closest(".input-group").find("input");
        $input.val( $input.val() - 1 );
    });
})
</script>