@extends('pages.layouts.app')

@section('css')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 text-center mt-auto">
            <div class="container d-flex justify-content-center align-items-center" style="height: 80vh;">
                <span class="text-danger">
                    <i class="menu-icon tf-icons ti ti-device-desktop-exclamation" style="font-size: 3rem"></i>
                    <p class="fw-semibold">User does not have the right permissions. Please Contact Admin/ Manager</p>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

