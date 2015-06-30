<?php
    $static_resources = \Ufa\Helpers\Resource::load_scripts();
    $main = \Ufa\Helpers\Resource::$host . 'dist/main.min.js';
    $main_ie = \Ufa\Helpers\Resource::$host . 'dist/main-ie.min.js';
    $params = \Ufa\Helpers\Resource::get_params();
?>

{{-- Load main script --}}
@if(\Ufa\Helpers\Resource::$compatible_ie)
    <!--[if IE]>
    <script src="{{$main_ie}}" type="text/javascript"></script>
    <![endif]-->
    <!--[if ! IE]><!-->
    <script src="{{$main}}" type="text/javascript"></script>
    <!--<![endif]-->
@else
    <script src="{{$main}}" type="text/javascript"></script>
@endif

<script type="text/javascript">
    $.params={!!json_encode($params)!!};
</script>

{{-- load external scripts --}}
@foreach($static_resources['external'] as $js_file)
    <script src="{{$dest_dir}}js/{{$js_file}}" type="text/javascript"></script>
@endforeach

{{-- load internal scripts --}}
@foreach($static_resources['internal'] as $js_file)
    <script src="{{$dest_dir}}js/{{$js_file}}" type="text/javascript"></script>
@endforeach