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
		<link rel="stylesheet" href="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/justifiedGallery.css" type="text/css" />
        <script type="application/javascript" 
		        src="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/jquery.justifiedGallery.js" />
		<?php if (class_exists('RSS')) printRSSHeaderLink('Album', getAlbumTitle()); ?>
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
				if(!zp_loggedin() && $loginlink = zp_apply_filter('login_link', getCustomPageURL('password'))) {
					$logintext = gettext('Login');
					$currentUrl = $_zp_current_album->getLink();
				?>
					<div id="login">
					    <input type="button" 
								class="button buttons"
								onclick="location.href='<?= $loginlink . '?from=' . $currentUrl; ?>'" 
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

				<h2>
					<span>
						<?php 
						    printHomeLink('', ' | '); 
						    printGalleryIndexURL(' | ', getGalleryTitle()); 
						    printParentBreadcrumb();
						?>
					</span>
					<?php printAlbumTitle(); ?>
				</h2>
			</div>
			<div id="padbox">
				<?php printAlbumDesc(); ?>
			</div>
			<div id="justified-gallery" class="justified-gallery">
				<?php while (next_album()): ?>
					<div class="passepartout">
						<a href="<?php echo html_encode(getAlbumURL()); ?>" 
						   title="<?php echo gettext('View album:'); ?> <?php printAnnotatedAlbumTitle(); ?>">
						   <?php printAlbumThumbImage(getAnnotatedAlbumTitle()); ?>
						</a>
						<div style="position: absolute; color: rgba(0, 0, 0, 0);">
							<?php printAnnotatedAlbumTitle(); ?>
						</div>
					</div>
				<?php endwhile; ?>
				<?php while (next_image()): ?>
					<div class="passepartout">
						<a href="<?php echo html_encode(getImageURL()); ?>" 
						   title="<?php printBareImageTitle(); ?>">
							<?php printImageThumb(getAnnotatedImageTitle()); ?>
						</a>
						<div style="position: absolute; color: rgba(0, 0, 0, 0);">
							<?php getAnnotatedImageTitle(); ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
			<br class="clearfloat">
			<?php
				printPageListWithNav("« " . gettext("prev"), gettext("next") . " »");
				if (function_exists('printAddToFavorites')) printAddToFavorites($_zp_current_album);
				printTags('links', gettext('<strong>Tags:</strong>') . ' ', 'taglist', '');
				@call_user_func('printOpenStreetMap');
				@call_user_func('printGoogleMap');
				@call_user_func('printSlideShowLink');
				@call_user_func('printRating');
				@call_user_func('printCommentForm');

				$thumbobj = $_zp_current_album->getAlbumThumbImage();
				$sizes = getSizeDefaultThumb($thumbobj);
				$rowheight = $sizes[1];
			?>
			<script type="application/javascript">
				$("#justified-gallery").justifiedGallery({
					rowHeight: <?= $rowheight ?>,
					lastRow: 'nojustify',
					margins: 55,
					waitThumbnailsLoad: false
				});
			</script>		
		</div>
		<?php include 'inc-footer.php'; ?>
	</body>
</html>