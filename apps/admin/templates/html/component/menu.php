<div class="container-fluid">
	<div class="navbar-header">
		<a class="navbar-brand" href="/admin/">Register Service</a>
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
			<?php foreach ($menu as $name => $url) : ?>
				<?php if (is_array($url)) : $subact = ''; ?>
				<?php foreach ($url as $subname => $suburl) { if ($suburl == $current) $subact = ' active'; } ?>
				<li class="dropdown<?php echo $subact.(isset($icon_class[$name]) ? ' menu-icon '.$icon_class[$name] : ''); ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="name"><?php echo $name ?></span> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
					<?php foreach ($url as $subname => $suburl) : //var_dump($suburl, $current); ?>
						<li<?php echo $suburl == $current ? ' class="active"' : ''; ?>><a href="<?php echo $suburl; ?>"><?php echo $subname ?></a></li>
					<?php endforeach; ?>
					</ul>
				</li>
				<?php else: ?>
				<li class="<?php echo ($url == $current ? 'active' : '').(isset($icon_class[$name]) ? ' menu-icon '.$icon_class[$name] : ''); ?>"><a href="<?php echo $url; ?>"><?php echo $name ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a class="navbar-right" href="/admin/logout"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
		</ul>
	</div>
</div>
