@extends('layouts.master')
@section('heading')
    {{ __('CV Control Panel') }}
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <h3>Registered Celebrities</h3>
        @foreach ($celebs as $name => $photos)
		<div class="celebrity">
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="<?='#'.str_replace(' ','-', $name)?>" aria-expanded="false" aria-controls="<?='#'.str_replace(' ','-', $name)?>">{{$name}}</button>
        <div class="collapse" id="<?=str_replace(' ','-', $name)?>">
            <div class="card card-body">
                @foreach ($photos as $photo)
                <div class="gallery">
                    <a target="_blank" href="<?='https://dbd.fofik.com/'.$photo?>">
                        <img src="<?='https://dbd.fofik.com/'.$photo?>" alt="Cinque Terre">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
		</div>
        @endforeach
    </div>
    <div class="col-md-6">
        <form action="{{route('cv.store')}}" method="POST" id="createTaskForm">
            @csrf
            <div class="form-group">    
                <h3>Add New</h3>
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

        <!-- Test recognition -->
        <div class="test-box" style="margin-top: 60px;">
        <h3>Test Box</h3>
        <input type="file" class="test-img" name="test-img" accept="image/*">

        <button class="recognize btn btn-primary" style="margin-top:20px">Test</button>
        </div>
    </div>
</div>
@stop

@push('scripts')
<style type="text/css">
    .celebrity {
		margin: 15px 0px;
	}
    .gallery {
        margin: 5px;
        border: 1px solid #ccc;
        float: left;
        width: 100px;
    }
</style>

<script>
    Dropzone.autoDiscover = false;
    $(function () {
        var myDropzone = new Dropzone("#createTaskForm", {
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 10,
            maxFiles: 50,
            addRemoveLinks: true,
            previewsContainer: "#dropzone-images",
            clickable:'#dropzone-images',
            paramName: 'images',
            acceptedFiles: "image/*",
        });

        myDropzone.on("success", function(file, response) {
            // window.location.href = ("/tasks/"+response.task_external_id)
            $('input[type="submit"]').attr("disabled", false);
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

        // Test recognition
        $('.recognize').click(function () {
            var formData = new FormData();
            formData.append('photo', $('.test-img')[0].files[0]);
            $.ajax({
                url: 'cv/test',
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    alert(result.name);
                }
            })
        })
    });
</script>
@endpush
