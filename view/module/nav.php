<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span> <span
					class="icon-bar"></span> <span class="icon-bar"></span> <span
					class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Aaron | Pengqi Xu</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse"
			id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li
					class="<?php echo $this->context->request['navButtonClass']['resume']; ?>"><a
					href="/">Resume</a></li>
				<li
					class="<?php echo $this->context->request['navButtonClass']['git']; ?>"><a
					href="repo">GitHub</a></li>
				<li
					class="<?php echo $this->context->request['navButtonClass']['widgets']; ?>"><a
					href="widgets">Widgets</a></li>
				<li class="dropdown" style="display: none;"><a href="#"
					class="dropdown-toggle" data-toggle="dropdown">Dropdown <b
						class="caret"></b>
				</a>
					<ul class="dropdown-menu">
						<li><a href="#">Action</a>
						</li>
						<li><a href="#">Another action</a>
						</li>
						<li><a href="#">Something else here</a>
						</li>
						<li class="divider"></li>
						<li><a href="#">Separated link</a>
						</li>
						<li class="divider"></li>
						<li><a href="#">One more separated link</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>
