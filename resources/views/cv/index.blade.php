@extends('layouts.master')
@section('heading')
    {{ __('CV Control Panel') }}
@stop

@section('content')
<form action="{{route('cv.store')}}" method="POST" id="createTaskForm">
    @csrf
    <div class="form-group">    
        <lablel class="control-label">Celebrity Name</label>
        <input type="text" class="form-control" name="name" placeholder="Type celebrity name here" />
    </div>
    <div class="form-group">
        <div class="tablet">
            <div class="tablet__body">
                    <label class="control-label">
                        <h3>Drop files here or click to upload</h3>
                    </label>
                    <div class="dropzone dz-default dz-message" id="dropzone-images">
                    </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Upload" />
    </div>
</form>
@stop

@push('scripts')
<style type="text/css">

</style>

<script>
    Dropzone.autoDiscover = false;
    $(function () {
        var myDropzone = new Dropzone("#createTaskForm", {
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFiles: 50,
            addRemoveLinks: true,
            previewsContainer: "#dropzone-images",
            clickable:'#dropzone-images',
            paramName: 'images',
            acceptedFiles: "image/*",

        });

        myDropzone.on("success", function(file, response) {
            // window.location.href = ("/tasks/"+response.task_external_id)
            console.log(file);
        });

        myDropzone.on("processing", function(file, response) {
            $('input[type="submit"]').attr("disabled", true);
        });
        myDropzone.on("error", function(file, response) {
            $('input[type="submit"]').attr("disabled", false);
        });

        $('input[type=submit]').click(function (e) {
            e.preventDefault();
            if (myDropzone.getQueuedFiles().length > 0)
                myDropzone.processQueue();
            else
                alert("Please select image files");
        })
    });
</script>
@endpush
