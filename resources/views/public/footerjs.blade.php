<script type="text/javascript" src="{{URL::asset('js/screenfull.min.js')}}"></script>
<script type="text/javascript" charset="utf-8">
		$(".navbar-nav").on('click', '.fa-dedent', function() {
			$.cookie('fa-indent', 1, {expires:7,path:'/'});
			$(this).removeClass('fa-dedent').addClass('fa-indent');
			$("#app").addClass('app-aside-folded');
			$(".navbarmenu").addClass('navbarmenufold');
		});
		$(".navbar-nav").on('click', '.fa-indent', function() {
			$.cookie('fa-indent', 0, {expires:7,path:'/'});
			$(this).removeClass('fa-indent').addClass('fa-dedent');
			$("#app").removeClass('app-aside-folded')
			$(".navbarmenu").removeClass('navbarmenufold');
		});
		var element, childrenUl, navtop;
		$(".navi-wrap")
				.on(
						"mouseover",
						'.navbarmenufold li',
						function() {
							$(this).addClass('active');
							childrenUl = $(this).find('ul');
							navtop = $(this).offset().top;
							$(".aside-wrap").next('ul').remove();
							if (childrenUl.html()) {
								element = '<ul class="nav nav-sub dk subnavmenu" style="top: '+ navtop +'px;">';
								element += childrenUl.html();
								element += '</ul>';
								childrenUl.remove();
								$(".aside-wrap").after(element);
							}
						})
		$(".navi-wrap").on("mouseout", '.navbarmenufold li', function() {
			$(this).removeClass('active');
			$(this).append(childrenUl);
		})
		$(".navi-wrap").on("click", '.navbarmenu li', function(event, a, b) {

			$(".navbarmenu li").removeClass('active');
			if ($(this).attr('status') == 'on') {
				$(this).removeClass('active').attr('status', 'off');
			} else {
				$(this).addClass('active').attr('status', 'on');
			}

		});
		$(".app-aside").on("mouseleave", '.subnavmenu', function() {
			$(this).remove();
		})
		$(".self-adaption-button").click(function(){
			var admenu = $("#self-adaption-menu");
			if(admenu.data('status')=='on'){
				admenu.removeClass('off-screen').data('status', 'off');
			}else{
				admenu.addClass('off-screen').data('status', 'on');
			}
		})
		//全屏显示
		var btn = document.getElementById('fullscreen');
		var content = document.getElementById('app');
		if(btn){
			btn.onclick = function() {
				if($(this).data.status=='on'){
					$(this).data.status = 'off';
					exitFullScreen(content);
				}else{
					$(this).data.status = 'on';
					fullScreen(content);
				}
			}
		}
		
	</script>