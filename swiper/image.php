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
		<link rel="stylesheet" href="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/common.css?ver=2" type="text/css" />
		<link rel="stylesheet" href="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/swiper-bundle.css?ver=8.4.5" type="text/css" />
        <script type="application/javascript" src="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/swiper-bundle.js?ver=8.4.5" ></script>
		<!-- script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script -->
		<?php if (zp_has_filter('theme_head', 'colorbox::css')) { ?>
			<script type="text/javascript">
				// <!-- <![CDATA[
				$(document).ready(function() {
					$(".fullimage").colorbox({
						maxWidth: "98%",
						maxHeight: "98%",
						photo: true,
						close: '<?php echo gettext("close"); ?>',
						onComplete: function () {
							$(window).resize(resizeColorBoxImage);
						}
					});
				});
				// ]]> -->
			</script>
		<?php } ?>
		<?php if (class_exists('RSS')) printRSSHeaderLink('Gallery', gettext('Gallery RSS')); ?>
	</head>
	<body>
		<?php 
		zp_apply_filter('theme_body_open'); 
		
		$images = $_zp_current_album->getImages(); 

		$currentImage = $_zp_current_image->getFileName();
		$initialIndex = 0;
		foreach ($images as $i):
			if ($i == $currentImage)
				break;
			$initialIndex++;
		endforeach;
		?>
		<div id="main">
			<div class="swiper">
				<div class="swiper-wrapper"></div>			
			</div>			
		</div>			
		<?php
			$modal_class = '';
		?> 

    <script type="application/javascript">
		// <!-- <![CDATA[
        $(document).ready(function () {
            //initialize swiper when document ready
            const swiper = new Swiper('.swiper', {
                initialSlide: <?= $initialIndex ?>,
				preloadImages: false,
				watchSlidesProgress: true,
				lazy: {
					enabled: true,
					loadPrevNext: true,
					loadPrevNextAmount: 1					
				},
                keyboard: {
                    enabled: true,
                    onlyInViewport: false,
                },
				virtual: {
					enabled: true,
					addSlidesAfter: 2,
					addSlidesBefore: 2,
					cache: true,
					renderExternalUpdate: true,
					slides: [
						<?php
							foreach ($images as $i):
								$image = newImage($_zp_current_album, $i);
								makeImageCurrent($image);
								
								$imageMetaData = json_encode(getImageMetaData());
																
								echo '{ fileName: \'' . $_zp_current_image->getFileName() . '\', '
									. 'imageTitle: \'' . html_encodeTagged(getImageTitle()) . '\', '
									. 'bareImageTitle: \'' . html_encode(getBareImageTitle()) . '\', '
									. 'width: ' . getDefaultWidth() . ', '
									. 'height: ' . getDefaultHeight() . ', '
									. 'defaultSizeImageUrl: \'' . html_encode(pathurlencode(getSizedImageURL(getOption('image_size')))) . '\', '
									. 'fullSizeImageUrl: ' . (isImagePhoto() && isset($_zp_current_admin_obj) ? '\'' . html_encode(pathurlencode(getFullImageURL())) . '\'' : 'null') . ', '
									. 'imageDesc: `' . html_encodeTagged(getImageDesc()) . '`, '
									. 'metadata: ' . $imageMetaData . ', '
								. ' }, '
								. PHP_EOL;
							endforeach;
						?>
				    ],
					renderSlide: createSlide
				},
				on: {
					afterInit: setupMetadataLink,
					slideChangeTransitionEnd: slideChangeTransitionEnd,
					activeIndexChange: setupMetadataLink,
					lazyImageReady: unhideImage
				}					
            });
			
			function slideChangeTransitionEnd() {
			    filename = $('.swiper-slide-active').attr('id');
			    if (filename) {
				    window.history.pushState({href: filename}, '', filename);
			    }
			    setupMetadataLink();			   
			}
			
			function setupMetadataLink() {
				$(".metadata_link").unbind("click");
				$(".metadata_link").click(function(event) { 
					event.preventDefault(); 
					let metadataClass = $(event.target).attr('metadataclass');
					$("." + metadataClass).toggle();
				});
			}
			
			function unhideImage(swiper, slideEl, imageEl) { 
				$(imageEl).css({visibility: 'visible'}); 
			}		
				
			function createSlide(slide, index) {
				var output = `
					<div class="swiper-slide" id="${slide.fileName}">
						<div id="gallerytitle">
						
						<?php
						if(!zp_loggedin() && $loginlink = zp_apply_filter('login_link', getCustomPageURL('password'))) {
							$logintext = gettext('Login');
							$currentUrl = $_zp_current_image->getLink();
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

							<h2>
								<span>
									<?php
									printHomeLink('', ' | ');
									printGalleryIndexURL(' | ', getGalleryTitle());
									printParentBreadcrumb("", " | ", " | ");
									printAlbumBreadcrumb("", " | ");
									?>
								</span>
								${slide.imageTitle}
							</h2>
						</div>
						<div id="image">
							<strong>`;
							if (slide.fullSizeImageUrl) {
								output += `
								<a href="${slide.fullSizeImageUrl}" title="${slide.bareImageTitle}" class="fullimage" target="_blank">`;
							}
							
							output += `
									<img data-src="${slide.defaultSizeImageUrl}"
										 alt="${slide.imageTitle}" 
										 title="${slide.imageTitle}" 
										 width="${slide.width}" 
										 height="${slide.height}"
										 class="swiper-lazy"/>
									<div class="swiper-lazy-preloader"></div>`;
							if (slide.fullSizeImageUrl) {
								output += `
								</a>`;
							}
									
							output += `
							</strong>
						</div>
						<div id="narrow" class="image_description">
						    <span>${slide.imageDesc}</span>
							<br class="clearall" />
							<hr/>
							<br class="clearall" />`;
							
							let metadataClass = 'metadata_' + index;
							let title = '<?= gettext('Image Info') ?>';
							output += `
							<span id="exif_link" class="metadata_title">
								<a href="#" class="metadata_link" title="${title}" metadataclass="${metadataClass}">${title}</a>
							</span>
							<div class="${metadataClass}" style="display: none;">
								<div id="imageMetadata">
									<div>
										<table>`;
											for (let key in slide.metadata) {
												let value = slide.metadata[key];
												output += `<tr><td class=\"label\">${key}</td><td class=\"value\">${value}</td>\n`;
											}
											output += `
										</table>
									</div>
								</div>
							</div>
						</div>					
						<br class="clearall"/>
					</div>`;

				return output;
			}
		});

		// ]]> -->				
    </script>

    <?php include 'inc-footer.php'; ?>
	</body>
</html>
