<?php
// force UTF-8 Ø

if (!defined('WEBPATH'))
	die();
?>
<!DOCTYPE html>
<html<?php printLangAttribute(); ?>>
	<head>
		<meta charset="<?php echo LOCAL_CHARSET; ?>">
		<?php zp_apply_filter('theme_head'); ?>
		<?php printHeadTitle(); ?>
		<link rel="stylesheet" href="<?php echo pathurlencode($zenCSS); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/common.css" type="text/css" />
		<?php if (class_exists('RSS')) printRSSHeaderLink('Gallery', gettext('Gallery RSS')); ?>
	</head>
	<body>
		<?php zp_apply_filter('theme_body_open'); ?>
		<!-- Hack to make it display correctly -->
		<div style="display:none">
			<div style="display:none">
				<form style="display:none">
					<script type="text/javascript" style="display:none">
					</script>
					<div style="display:none">
						<span class="tagSuggestContainer" style="display:none">
						</span>
					</div>
				</form>
			</div>
		</div>
		<div id="main">
			<div id="gallerytitle">
				<?php
				if(!zp_loggedin()) {
                    $loginlink = zp_apply_filter('login_link', getCustomPageURL('password'));
                    $logintext = gettext('Login');
				?>
					<div id="login">
					    <input type="button" 
								class="button buttons"
								onclick="location.href='<?= $loginlink ?>'" 
								value="<?= $logintext?>" />
					</div>
				<?php 
					} 

					$timelinetext = gettext('Timeline');
					$timelinelink = getCustomPageURL('archive');
				?>
					<div id="timeline">
					    <input type="button" 
								class="button buttons"
								onclick="location.href='<?= $timelinelink?>'" 
								value="<?= $timelinetext?>" />
					</div>
				<?php 
					if (getOption('Allow_search')) {
						printSearchForm(); 
					}
				?>
				<h2><?php
					printHomeLink('', ' | ');
					printGalleryTitle();
                    printCurrentPageAppendix();
                ?></h2>
			</div>
			<div id="padbox">
				<?php
                    printGalleryDesc();
                    $passepartoutBorder = getOption('Album_passepartout_border');
                    $galleryMargin = getOption('Gallery_margin') - $passepartoutBorder * 2;
                ?>
				<div id="albums">
					<?php while (next_album()): ?>
						<div class="album passepartout with-description" style="border-width:<?= $passepartoutBorder ?>px; margin-right: <?= $galleryMargin ?>px">
							<div class="thumb">
								<a href="<?php echo html_encode(getAlbumURL()); ?>" 
								   title="<?php echo gettext('View album:'); ?> <?php printAnnotatedAlbumTitle(); ?>">
								   <?php printAlbumThumbImage(getAnnotatedAlbumTitle()); ?>
								</a>
							</div>
							<div class="albumdesc">
								<h3><a href="<?php echo html_encode(getAlbumURL()); ?>" title="<?php echo gettext('View album:'); ?> <?php printAnnotatedAlbumTitle(); ?>"><?php printAlbumTitle(); ?></a></h3>
								<small><?php printAlbumDate(""); ?></small>
								<div><?php printAlbumDesc(); ?></div>
							</div>
							<p style="clear: both; "></p>
						</div>
					<?php endwhile; ?>
				</div>
				<br class="clearall">
				<?php printPageListWithNav("« " . gettext("prev"), gettext("next") . " »"); ?>
			</div>
		</div>
		<?php include 'inc-footer.php'; ?>
	</body>
</html>