@extends('layouts.master')

@section('content')
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#modal-create-client').modal({backdrop: 'static', keyboard: false})
            $('#modal-create-client').modal('show');

            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true,

                });
            });
        });
    </script>
@endpush
        <!-- Small boxes (Stat box) -->
        @if(isDemo())
            <div class="alert alert-info">
                <strong>Info!</strong> Data on the demo environment is reset every 24hr.
            </div>
        @endif

        <div class="row">
        </div>
        <!-- /.row -->
@endsection
