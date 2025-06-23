<!DOCTYPE html>
<html>
<?php include 'widget_head.php' ?>
<body>
<div class='page'>
	<?php include('widget_header.php') ?>
	<?php $this->includeWidgets([['sitemenu', 'parameter'=>['menutype'=>'top']]], $this->performerResults); ?>
	<div class='main'>
		<div class='top'>
			<?php include('widget_errors.php') ?>
			<?php include('widget_sitenotice.php') ?>
			<?php include('widget_usernotice.php') ?>
		</div>
		<div class='middle'>
			<div class='left'>
				<?php $this->includeWidgets($this->widgetsLeft, $this->performerResults); ?>
			</div>
			<div class='right'>
				<?php $this->includeWidgets($this->widgetsRight, $this->performerResults); ?>
			</div>
		</div>
		<?php $this->includeWidgets($this->widgetsBottom, $this->performerResults); ?>
	</div>
	<footer><?php $this->includeWidgets([['sitemenu', 'parameter'=>['menutype'=>'bottom']]], $this->performerResults); ?></footer>
	<?php include('widget_footer.php') ?>
</div>
</body>
</html>