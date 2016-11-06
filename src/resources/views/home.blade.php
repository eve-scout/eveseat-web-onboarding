@push('full')
<div class="grid-stack-item"
        data-gs-width="6"
        data-gs-height="10"
        id="dg-1">
  <div class="grid-stack-item-content">
    <div class="box box-solid">
      <div class="box-header with-border bg-blue-active">
        <h3 class="box-title">Getting Started</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body no-padding">
        <ul class="nav nav-stacked">
          <li @if($isAccountActive) class="list-group-item-success" @endif><a href="/profile/settings">Activate account by following link sent to your email address <span class="pull-right fa fa-check-circle @if($isAccountActive) text-green @else text-muted @endif"></span></a></li>
          <li @if($hasApiKeys) class="list-group-item-success" @endif><a href="/api-key/add">Add EVE API Key <span class="pull-right fa fa-check-circle @if($hasApiKeys) text-green @else text-muted @endif"></span></a></li>
          <li @if(! is_null(setting('main_character_name'))) class="list-group-item-success" @endif><a href="/profile/settings">Associate Main Character <span class="pull-right fa fa-check-circle @if(is_null(setting('main_character_name'))) text-muted @else text-green @endif"></span></a></li>
          <li @if($hasVisitedForums) class="list-group-item-success" @endif><a href="https://forums.eve-scout.com">Visit Forums and Login<span class="pull-right fa fa-check-circle @if($hasVisitedForums) text-green @else text-muted @endif"></span></a></li>
        </ul>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>

<div class="grid-stack-item"
        data-gs-width="6"
        data-gs-height="10"
        id="dg-4">
  <div class="grid-stack-item-content">
    <div class="info-box">
      <span class="info-box-icon bg-green"><i class="fa fa-key"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ trans('web::seat.owned_api_keys') }}</span>
        <span class="info-box-number">
          {{ count(auth()->user()->keys) }}
        </span>
        <a href="/api-key/add" class="btn btn-sm pull-right bg-green">
          Add a EVE API Key
        </a>
      </div><!-- /.info-box-content -->
    </div><!-- /.info-box -->
  </div>
</div>
@endpush