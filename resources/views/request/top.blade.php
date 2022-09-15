@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
<div class="form-horizontal">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group select-type">
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="move" class="btn btn-lg btn-light btn-default btn-block">拠点間移動/Movement between Location</a>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="internal-move" class="btn btn-lg btn-light btn-default btn-block">拠点内移動/Movement within Location</a>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="carry-in" class="btn btn-lg btn-light btn-default btn-block">搬入/Move In</a>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="carry-out" class="btn btn-lg btn-light btn-default btn-block">搬出/Move Out</a>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="disposal" class="btn btn-lg btn-light btn-default btn-block">廃棄/Dispose</a>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="sale" class="btn btn-lg btn-light btn-default btn-block">売却/Resale</a>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:10px;">
                    <a href="{{$routeUri}}/" data-type="change-classification" class="btn btn-lg btn-light btn-default btn-block">区分変更/Change grade</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row hidden">
        <div class="col-md-12 text-center">
            <h3>家具種類を選択</h3>
        </div>
    </div>
    <div class="row hidden">
        <div class="col-md-12">
            <div class="form-group select-furniture">
                <div id="soft" class="col-xs-6 col-sm-6 col-md-6" style="margin-bottom:10px;">
                    <a data-link_base="{{$routeUri}}/soft/table/" class="btn btn-lg btn-light btn-default btn-block">ソフトファニチャー/Soft Furniture</a>
                </div>
                <div id="hard" class="col-xs-6 col-sm-6 col-md-6" style="margin-bottom:10px;">
                    <a data-link_base="{{$routeUri}}/hard/table/" class="btn btn-lg btn-light btn-default btn-block">ハードファニチャー/Hard Furniture</a>
                </div>
				<div id="ather" class="col-xs-4 col-sm-4 col-md-4 d-none" style="margin-bottom:10px;">
                    <a data-link_base="{{$routeUri}}/ather/table/" class="btn btn-lg btn-light btn-default btn-block">その他/Others</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>jQuery(document).ready(
		a=>{
			const b=a(".select-type"),
				  f=a(".select-furniture"),
				  g=b.find("a"),
				  h=f.find("a");
			
			b.on("click","a",
				 c=>{
					c.preventDefault();
					 g.removeClass("active");
					 
					 const d=a(c.target);
					 d.addClass("active");
				
					if(d.data("type") == "disposal" || d.data("type") == "move") {
						$("#ather").removeClass("d-none");
						
						$("#soft").removeClass("col-xs-6 col-sm-6 col-md-6");
						$("#hard").removeClass("col-xs-6 col-sm-6 col-md-6");
						$("#soft").addClass("col-xs-4 col-sm-4 col-md-4");
						$("#hard").addClass("col-xs-4 col-sm-4 col-md-4");
					}
					else {
						$("#ather").addClass("d-none");
						
						if($('#soft').hasClass('col-xs-4')){
							$("#soft").addClass("col-xs-6 col-sm-6 col-md-6");
							$("#hard").addClass("col-xs-6 col-sm-6 col-md-6");
							$("#soft").removeClass("col-xs-4 col-sm-4 col-md-4");
							$("#hard").removeClass("col-xs-4 col-sm-4 col-md-4");
						}
					}
				
					 h.each(function(k,e) {
						 a(e).attr("href",a(e).data("link_base")+d.data("type"));
						 
					 })
					 //h.each((k,e)=>a(e).attr("href",a(e).data("link_base")+d.data("type")));
					 a(".hidden").removeClass("hidden")
					}
				)
		}
	);
</script>
@endsection