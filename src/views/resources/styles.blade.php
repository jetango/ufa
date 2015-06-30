<?php
    $static_resources = \Ufa\Helpers\Resource::load_styles();
    $main = \Ufa\Helpers\Resource::$host . 'dist/main.min.css';
    $main_ie = \Ufa\Helpers\Resource::$host . 'dist/main-ie.min.css';
?>

{{-- Load main style --}}
@if(\Ufa\Helpers\Resource::$compatible_ie)
    <!--[if IE]>
    <link href="{{$main_ie}}" rel="stylesheet"/>
    <![endif]-->
    <!--[if ! IE]><!-->
    <link href="{{$main}}" rel="stylesheet"/>
    <!--<![endif]-->
@else
    <link href="{{$main}}" rel="stylesheet"/>
@endif

{{-- load external styles --}}
@foreach($static_resources['external'] as $css_file)
    <link href="{{$dest_dir}}css/{{$css_file}}" rel="stylesheet"/>
@endforeach

{{-- load internal styles --}}
@foreach($static_resources['internal'] as $css_file)
    <link href="{{$dest_dir}}css/{{$css_file}}" rel="stylesheet"/>
@endforeach