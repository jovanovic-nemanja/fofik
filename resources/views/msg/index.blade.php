@extends('layouts.master')
@section('heading')
    {{ __('Message Panel') }}
@stop

@section('content')
<div class="container">
    <div class="row message-box">  
        <div class="form-group">
            <label>Message Type: </label>
            <select class="form-control" id="msg-type">
                <option value="M">Mail</option>
                <option value="PN">Notification</option>
            </select>
        </div>
        <div class="form-group">
            <label>Title: </label>
            <input type="text" class="form-control" name="title" id="title"/>
        </div>
        <div class="form-group">
            <label>Content: </label>
            <textarea class="form-control" name="content" id="content" rows="5" cols="100"></textarea>
        </div>
        <div class="form-group pull-right">
            <button class="btn btn-primary send">SEND</button>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.send').click(function () {
            let type = $('#msg-type').val();
            let title = $('#title').val();
            let content = $('#content').val();
            if (!title || !content) {
                alert("Please confirm message to be filled");
                return;
            }
            $.ajax({
                url: 'message/send',
                type: 'post',
                data: {
                    type: type,
                    title: title,
                    content: content
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
    })
</script>
@endpush