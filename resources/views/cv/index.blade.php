@extends('layouts.master')
@section('heading')
    {{ __('CV Control Panel') }}
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <h3>Registered Celebrities</h3>
        <select class="celeb-list selectpicker" data-live-search="true">
            @foreach ($celebs as $each)
                <option value="<?=$each->id?>">{{ $each->name }}</option>
            @endforeach
        </select>
        <div class="card card-body">
        </div>
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
        display: grid;
	}
    .gallery {
        margin: 5px;
        float: left;
        width: 120px;
        border-radius: 20px;
    }
    .gallery img {
        border-radius: 20px;
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

        function drawPhotoBoard(photos)
        {   
            $('.card').empty();
            for (var i = 0; i < photos.length; i++) {
                let photo = photos[i];
                let imgLink = 'https://dbd.fofik.com/' + photo;
                let gallery = document.createElement('div');
                let linkTag = document.createElement('a');
                let imgTag = document.createElement('img');

                gallery.setAttribute('class', 'gallery');
                linkTag.setAttribute('target', '_blank');
                linkTag.setAttribute('href', imgLink);
                imgTag.setAttribute('src', imgLink);
                imgTag.setAttribute('width', '120px');
                imgTag.setAttribute('height', '120px');
                linkTag.appendChild(imgTag);
                gallery.appendChild(linkTag);
                $('.card').append(gallery);
            }
        }
        function emptyPhotoBoard()
        {
            $('.card').empty();
            let empText = "There are no photos";
            $('.card').append(empText);
        }
        $('.celeb-list').change(function () {
            let id = $(this).val();
            $.ajax({
                url: 'cv/photos',
                type: 'get',
                data: {
                    id: id
                },
                success: function (result) {
                    if (result.success) {
                        drawPhotoBoard(result.photos);
                    } else {
                        emptyPhotoBoard();
                    }
                }
            })
        })
        $('.celeb-list').val(1).change();
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
