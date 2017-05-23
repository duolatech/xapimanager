
<!-- #section:basics/sidebar -->
<div id="sidebar" class="sidebar responsive">
	<script type="text/javascript">
            try {
                ace.settings.check('sidebar', 'fixed')
            } catch (e) {
            }
        </script>

	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<button class="btn btn-widget btn-success" xhref="/Api/list">
				<i class="ace-icon fa fa-bars"></i>
			</button>
			<button class="btn btn-widget btn-info" xhref="/Api/info">
				<i class="ace-icon fa fa-pencil"></i>
			</button>
			<button class="btn btn-widget btn-warning" xhref="/User/index">
				<i class="ace-icon fa fa-users"></i>
			</button>
			<button class="btn btn-widget btn-danger" xhref="/Sys/site">
				<i class="ace-icon fa fa-cogs"></i>
			</button>
			<!-- /section:basics/sidebar.layout.shortcuts -->
		</div>

		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<span class="btn btn-success"></span> <span class="btn btn-info"></span>

			<span class="btn btn-warning"></span> <span class="btn btn-danger"></span>
		</div>
	</div>
	<!-- /.sidebar-shortcuts -->

	<ul class="nav nav-list">
	
		@foreach($sys['ValidMenu'] as $menu)
			<li class="@if(!empty($sys['Router'][Request::path()]['id']) && $sys['Router'][Request::path()]==$menu['id'] ) active @endif
				@if(!empty($sys['Router'][Request::path()]) && $sys['Router'][Request::path()]['id']==$menu['id'] && !empty($menu['child'])) open @endif">
				<a href="{{$menu['path']}}" @if(!empty($menu['child'])) class="dropdown-toggle"  @endif> 
					<i class="{{$menu['icon']}}"></i> 
					<span class="menu-text">{{$menu['title']}}</span>
					@if(!empty($menu['child'])) <b class="arrow fa fa-angle-down"></b>   @endif
				</a> 
					@if(!empty($menu['child'])) 
						<ul class="submenu">
        					@foreach($menu['child'] as $submenu)
        						<li @if($submenu['path'] == '/'.Request::path()) class="active" @endif>
            						<a href="{{$submenu['path']}}">
                    						<i class=""></i> {{$submenu['title']}}
                    				</a> 
                    				<b class="arrow"></b>
                				</li>
        					@endforeach
    					</ul>
					@endif
					
				
				<b class="arrow"></b>
			</li>
		@endforeach
	
	</ul>
	<!-- /.nav-list -->

	<!-- #section:basics/sidebar.layout.minimize -->
	<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
		<i class="ace-icon fa fa-angle-double-left"
			data-icon1="ace-icon fa fa-angle-double-left"
			data-icon2="ace-icon fa fa-angle-double-right"></i>
	</div>

	<!-- /section:basics/sidebar.layout.minimize -->
	<script type="text/javascript">
            try {
                ace.settings.check('sidebar', 'collapsed')
            } catch (e) {
            }
            $(".btn-widget").click(function(){
				var url = $(this).attr('xhref');
				window.location.href=url;
            })
        </script>
</div>
<!-- /section:basics/sidebar -->