<!-- DESKTOP NAV --->
<button type="button" class="navbar-toggle menu-txt-toggle" style="">
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
</button>

<!-- MOBILE NAV -->
<button type="button" id="mobile-toggle" class="mobile-toggle mobile-nav" data-toggle="offcanvas"
        data-target="#myNavmenu">
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
</button>

<div class="navbar navbar-default navbar-top">

        <div class="navbar-icons__wrapper">

            <div id="nav-toggle col-sm-6">
                <search></search>
            </div>
            <div id="nav-toggle col-sm-4">
                <a href="{{route('dashboard')}}" style="text-decoration: none;">
                    <span class="top-bar-toggler">
                        <i class="flaticon-gear"></i>
                    </span>
                </a>
            </div>
            @include('navigation.topbar.user-profile')
            <div id="nav-toggle col-sm-2">
                <a id="grid-action" role="button" data-toggle="dropdown">
                    <span class="top-bar-toggler">
                        <i class="flaticon-grid"></i>
                    </span>
                </a>
            </div>
        </div>

    @include('partials.action-panel._panel')
<!--NOTIFICATIONS END-->
</div>

