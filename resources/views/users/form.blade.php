
<div class="col-sm-3">
    <label for="name" class="base-input-label">@lang('Name')</label>
</div>
<div class="col-sm-9">
        <div class="form-group col-sm-8">
            <input type="text" name="name" class="form-control" value="{{isset($user) ? optional($user)->name : ''}}">
        </div>
</div>
<div class="col-sm-12">
    <hr>
</div>
<div class="col-sm-3">
    <label for="image_path" class="base-input-label">@lang('Image')</label>
</div>
<div class="col-sm-9">
    <div class="form-group form-inline col-sm-8">
        <div class="input-group ">
            <img id="preview_avatar" src="{{isset($user) ? optional($user)->avatar : '/images/default_avatar.jpg'}}" style="max-height: 40px; border-radius: 25px;">
        </div>
        <div id="input_avatar" class="input-group" style="margin-left: 0.7em;">
            <input type="file" name="image_path" id="avatar_image" onchange="loadPreview(this);">
            <span style="font-size:10px">Recommended size 300x300</span>
        </div>
        <div class="input-group" style="margin-left: 0.7em;">
            <button id="delete_avatar" type="button">remove</button>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <hr>
</div>
<div class="col-sm-3">
    <label for="name" class="base-input-label">@lang('Contact information')</label>
</div>
<div class="col-sm-9">
    <div class="form-group col-sm-8">
        <label for="email" class="control-label thin-weight">@lang('Email')</label>
        <input type="email" name="email" class="form-control" value="{{isset($user) ? optional($user)->email : ''}}">
    </div>
</div>

<div class="col-sm-12">
    <hr>
</div>
<div class="col-sm-3">
    <label for="name" class="base-input-label">@lang('Social')</label>
</div>
<div class="col-sm-9">
    <div class="form-group col-sm-8">
        <label for="email" class="control-label thin-weight">@lang('ID')</label>
        <input type="text" name="social_id" class="form-control" value="{{isset($user) ? optional($user)->social_id : ''}}">
    </div>
    <div class="form-group col-sm-8">
        <label for="email" class="control-label thin-weight">@lang('Site')</label> 
        <input type="text" name="social_site" class="form-control" value="{{isset($user) ? optional($user)->social_site : ''}}">
    </div>
</div>
<div class="col-lg-12">
    <input type="submit" value="{{$submitButtonText}}" class="btn btn-md btn-brand">
</div>