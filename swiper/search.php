<?php
// force UTF-8
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
		<?php if (class_exists('RSS')) printRSSHeaderLink('Gallery', gettext('Gallery RSS')); ?>
	</head>
	<body>
		<?php
		zp_apply_filter('theme_body_open');
		$total = getNumImages() + getNumAlbums();
		if (!$total) {
			$_zp_current_search->clearSearchWords();
		}
		?>
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
					$currentUrl = getPageNumURL(getCurrentPage());
				?>
					<div id="login">
					    <input type="button" 
								class="button buttons"
								onclick="location.href='<?= $loginlink . '?from=' . $currentUrl; ?>'" 
								value="<?= $logintext ?>" />
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
						<?php printHomeLink('', ' | '); printGalleryIndexURL(' | ', getGalleryTitle()); ?></a>
					</span>
					<?php printSearchBreadcrumb(' | '); printCurrentPageAppendix(); ?>
				</h2>
			</div>
			<div id="padbox">
				<?php
				if (($total = getNumImages() + getNumAlbums()) > 0) {
					if (isset($_REQUEST['date'])) {
						$searchwords = getSearchDate();
					} else {
						$searchwords = getSearchWords();
					}
					echo '<p>' . sprintf(gettext('Total matches for <em>%1$s</em>: %2$u'), html_encode($searchwords), $total) . '</p>';
				}
				$c = 0;
				?>
			</div>
			<div id="justified-gallery" class="justified-gallery">
					<?php while (next_album()) {?>
						<div class="passepartout">
							<?php $c++; ?>
							<a href="<?php echo html_encode(getAlbumURL()); ?>" 
							   title="<?php printAnnotatedAlbumTitle(); ?>">
								<?php printAlbumThumbImage(getAnnotatedAlbumTitle()); ?>
							</a>
						</div>
					<?php } ?>
					<?php while (next_image()) {?>
						<div class="passepartout">
							<?php $c++; ?>
						        <a href="<?php echo html_encode(getImageURL()); ?>" 
							   title="<?php printBareImageTitle(); ?>">
							        <?php printImageThumb(getAnnotatedImageTitle()); ?>
							</a>
						</div>
					<?php } ?>
			</div>
				<br class="clearall">
				<?php
				callUserFunction('printSlideShowLink');
				if ($c == 0) {
					echo "<p>" . gettext("Sorry, no image matches found. Try refining your search.") . "</p>";
				}
				printPageListWithNav("« " . gettext("prev"), gettext("next") . " »");
				$rowheight = getOption('thumb_crop_height')
				?>
			<script type="application/javascript">
				$("#justified-gallery").justifiedGallery({
					rowHeight: <?= $rowheight ?>,
					lastRow: 'nojustify',
					margins: 55					
				});
			</script>
		</div>
		<?php include 'inc-footer.php'; ?>
	</body>
</html>