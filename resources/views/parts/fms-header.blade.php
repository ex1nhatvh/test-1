<header class="fms-header">
    <h2 class="wwj-logo"><img src="{{ route('exment.plugin.index') }}s/furniture_management_system/public/images/wwj_logo.svg"></h2>
    @if(!isset($hidden_back))
    <a class="btn btn-secondary btn-default btn-outline-secondary back" role="back"><i class="fa fa-chevron-left"></i></a>
    @endif
    <a class="btn btn-secondary btn-default btn-outline-secondary home" href="{{ route('exment.plugin.index') }}s/furniture_management_system/"><i class="fa fa-home"></i></a>
</header>