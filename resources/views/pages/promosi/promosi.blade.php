@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Master Data Promosi</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" id="tambahPromosi">
                                    + Promosi
                                </button>
                                <button class="btn btn-sm flex-fill btn-success">Export Excel</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        @include('pages.promosi.promosi-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.promosi.modal-promosi')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('lib') }}/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
@endpush
