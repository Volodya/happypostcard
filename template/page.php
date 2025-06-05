<!DOCTYPE html>
<html>
<?php include 'widget_head.php' ?>
<body>
<div class='page'>
	<?php include('widget_header.php') ?>
	<?php include('widget_menu.php') ?>
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
	<?php include('widget_sitemap.php') ?>
	<?php include('widget_footer.php') ?>
</div>
</body>
</html>