<header>
	<h1><?php echo ucfirst($module->name); ?> Module</h1>
	<div class="sparky_modal_close"><a href="#">close</a></div>
</header>
<nav>
    <ul>
    	<?php foreach(Sparky::modalNavigation($module) as $key => $link): ?>
    		<?php if(ucfirst($action) == $key): ?>
    			<li><a class="modal_nav active" href="<?php echo $link; ?>"><?php echo $key; ?></a></li>
    		<?php endif; ?>
    		<?php if(ucfirst($action) != $key): ?>
    			<li><a class="modal_nav" href="<?php echo $link; ?>"><?php echo $key; ?></a></li>
    		<?php endif; ?>
    	<?php endforeach; ?>
    </ul>
</nav>
<form method="post" action="m,<?php echo $module->name; ?>,<?php echo $module->id; ?>/save" id="modal_form">
	<div class="body">
		<?php echo $content; ?>
	</div>
	<input type="hidden" value="<?php echo $module->id; ?>" name="module_id">
	<div class="buttons">
		<input type="submit" value="Save">
	</div>
</form>