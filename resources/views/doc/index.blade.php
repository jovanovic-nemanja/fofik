@extends('layouts.master')
@section('heading')
    {{ __('AC Panel') }}
@stop

@section('content')
<div class="container">
    <div class="row upload-csv">
        <div class="">
            <h2 id="introHeader">Upload your CSV File</h2>	
            <fieldset>
                <input type="file" name="File Upload" id="csv" accept=".csv" />
            </fieldset> 
        </div>
        <div class="form-group pull-right">
            <button class="btn btn-primary send-csv">Go</button>
        </div>
    </div>
    <div class="row">
        <div class="input-group spinner">
            <input type="text" class="form-control" value="5" readonly>
            <div class="input-group-btn-vertical">
                <button class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                <button class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
            </div>
        </div>
        <div class="form-group name-box">
            <input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>
            <input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>
            <input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>
            <input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>
            <input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>
        </div>   
        <div class="form-group pull-right">
            <button class="btn btn-primary send">Go</button>
        </div>
    </div>
    <div class="row">  
        
    </div>
</div>
@stop

@push('scripts')
<style>
.row {
    margin: 15px;
}
.upload-csv {
    border-bottom: 1px solid #c3c3c3;
}
.spinner {
  width: 100px;
}
.spinner input {
  text-align: right;
}
.input-group-btn-vertical {
  position: relative;
  white-space: nowrap;
  width: 1%;
  vertical-align: middle;
  display: table-cell;
}
.input-group-btn-vertical > .btn {
  display: block;
  float: none;
  width: 100%;
  max-width: 100%;
  padding: 8px;
  margin-left: -1px;
  position: relative;
  border-radius: 0;
}
.input-group-btn-vertical > .btn:first-child {
  border-top-right-radius: 4px;
}
.input-group-btn-vertical > .btn:last-child {
  margin-top: -2px;
  border-bottom-right-radius: 4px;
}
.input-group-btn-vertical i{
  position: absolute;
  top: 0;
  left: 4px;
}
</style>
<script type="text/javascript">
(function ($) {
    $('.spinner .btn:first-of-type').on('click', function() {
        let rows = parseInt($('.spinner input').val(), 10);
        rows += 5;
        $('.spinner input').val(rows);
        $('.name-box').append(
            '<input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>' + 
            '<input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>' + 
            '<input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>' +
            '<input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>' + 
            '<input type="text" class="form-control" placeholder="Enter celebrity name" name="names[]"/>'
        );
    });
    $('.spinner .btn:last-of-type').on('click', function() {
        let rows = parseInt($('.spinner input').val(), 10);
        if (rows >= 10) {
            rows -= 5;
            $('.name-box').children().last().remove();
            $('.name-box').children().last().remove();
            $('.name-box').children().last().remove();
            $('.name-box').children().last().remove();
            $('.name-box').children().last().remove();
        }
        $('.spinner input').val(rows);
    });
    $('.send').click(function () {
        let inputs = $('.name-box').children();
        let vals = [];
        for (let i = 0; i < inputs.length;  i++)
        {   
            let val;
            if (val = $(inputs[i]).val()) {
                vals.push(val);
            }
        }
        console.log(vals);
        $.ajax({
            url: 'document/names',
            type: 'post',
            data: {
                names: vals
            },
            success: function (result) {
                if (result.success) {
                    alert("Successfully pushed");
                } else {
                    alert("Sorry, something went wrong");
                }
            }
        })
    })
    $('.send-csv').click(function () {
        var formData = new FormData();
        formData.append('csv', $('#csv')[0].files[0]);
        $.ajax({
            url: 'document/names',
            type: 'post',
            data: formData,
            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
            processData: false, // NEEDED, DON'T OMIT THIS
            success: function (result) {
                if (result.success) {
                    alert("Successfully pushed");
                } else {
                    alert("Sorry, something went wrong");
                }
            }
        })
    })
})(jQuery);
</script>
@endpush
