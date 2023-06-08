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
    <link rel="stylesheet" href="<?php echo pathurlencode($zenCSS); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/common.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/justifiedGallery.css"
          type="text/css"/>
    <script type="application/javascript"
            src="<?php echo pathurlencode(dirname(dirname($zenCSS))); ?>/jquery.justifiedGallery.js"/>
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
        if (!zp_loggedin() && $loginlink = zp_apply_filter('login_link', getCustomPageURL('password'))) {
            $logintext = gettext('Login');
            $currentUrl = $_zp_current_album->getLink();
            ?>
            <div id="login">
                <input type="button"
                       class="button buttons"
                       onclick="location.href='<?= $loginlink . '?from=' . $currentUrl; ?>'"
                       value="<?= $logintext ?>"/>
            </div>
            <?php
        }

        $timelinetext = gettext('Timeline');
        $timelinelink = getCustomPageURL('archive');
        ?>
        <div id="timeline">
            <input type="button"
                   class="button buttons"
                   onclick="location.href='<?= $timelinelink ?>'"
                   value="<?= $timelinetext ?>"/>
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
    <div id="justified-gallery" class="justified-gallery"></div>
    <?php
    $thumbobj = $_zp_current_album->getAlbumThumbImage();
    $sizes = getSizeDefaultThumb($thumbobj);
    $rowheight = $sizes[1];
    ?>
    <br class="clearfloat">
    <script type="application/javascript">
        const thumbnails = [
            <?php
            while (next_album()) {
                $thumbobj = $_zp_current_album->getAlbumThumbImage();
                $thumbSizes = getSizeDefaultThumb($thumbobj);

                echo "{ 'type': 'album',"
                    . "'thumbUrl': '" . html_pathurlencode($thumbobj->getThumb('album')) . "',"
                    . "'albumUrl': '" . html_pathurlencode(getAlbumURL()) . "',"
                    . "'albumTitle': '" . html_encode(getAnnotatedAlbumTitle()) . "',"
                    . "'thumbWidth': " . $thumbSizes[0] . ","
                    . "'thumbHeight': " . $thumbSizes[1] . " },"
                    . PHP_EOL;
            }
            while (next_image()) {
                $sizes = getSizeDefaultThumb($_zp_current_image);

                echo "{ 'type': 'image',"
                    . "'thumbUrl': '" . html_pathurlencode($_zp_current_image->getThumb()) . "',"
                    . "'imageUrl': '" . html_pathurlencode(getImageURL()) . "',"
                    . "'imageTitle': '" . html_encode(getAnnotatedImageTitle()) . "',"
                    . "'thumbWidth': " . $sizes[0] . ","
                    . "'thumbHeight': " . $sizes[1] . " },"
                    . PHP_EOL;
            }
            ?>
        ];

        let justifiedGalleryElement = $("#justified-gallery");

        const margin = 65;
        const rowHeight = <?= $rowheight ?>;
        const rowCount = calculateRowCount(rowHeight);
        const thumbsPerRow = calculateThumbsPerRow(margin);
        const thumbCount = rowCount * thumbsPerRow;
        let firstThumb = 0;
        const passepartouts = thumbnails.slice(firstThumb, thumbCount).map(createPassepartout).join('');

        function calculateRowCount(rowHeight) {
            const viewportHeight = $(window).height() - justifiedGalleryElement.position().top;
            return Math.ceil(viewportHeight / rowHeight);
        }

        function calculateThumbsPerRow(margin) {
            const thumbWidth = thumbnails.map(t => t.thumbWidth).reduce((a, b) => Math.max(a, b)) + margin;
            const viewportWidth = justifiedGalleryElement.innerWidth();
            return Math.floor(viewportWidth / thumbWidth);
        }

        justifiedGalleryElement.append(passepartouts);
        firstThumb += thumbCount;
        initialiseJustifiedGallery();

        let previousScrollY = 0;
        window.addEventListener("scroll", function() {
            if (window.scrollY > previousScrollY + rowHeight) {
                const passepartouts = thumbnails.slice(firstThumb, firstThumb + thumbsPerRow).map(createPassepartout).join('');
                justifiedGalleryElement.append(passepartouts);
                firstThumb += thumbsPerRow;
                previousScrollY += rowHeight;
                loadNewImages();
            }
        });

        function createPassepartout(thumb) {
            if (thumb.type === 'album') {
                return createAlbumPassepartout(thumb);
            } else {
                return createImagePassepartout(thumb);
            }
        }

        function createAlbumPassepartout(thumb) {
            return `
                <div class="passepartout">
                    <a href="${thumb.albumUrl}"
                       title="View album: ${thumb.albumTitle}">
                       <img src="${thumb.thumbUrl}" width="${thumb.thumbWidth}" height="${thumb.thumbHeight}" alt="${thumb.albumTitle}" loading="lazy"/>
                    </a>
                    <div style="position: absolute; color: rgba(0, 0, 0, 0);">${thumb.albumTitle}</div>
                </div>
                `;
        }

        function createImagePassepartout(thumb) {
            return `
                <div class="passepartout">
                    <a href="${thumb.imageUrl}"
                       title="${thumb.imageTitle}">
                       <img src="${thumb.thumbUrl}" width="${thumb.thumbWidth}" height="${thumb.thumbHeight}" alt="${thumb.imageTitle}" loading="lazy"/>
                    </a>
                    <div style="position: absolute; color: rgba(0, 0, 0, 0);">${thumb.albumTitle}</div>
                </div>
                `;
        }

        function initialiseJustifiedGallery() {
            justifiedGalleryElement.justifiedGallery({
                rowHeight: <?= $rowheight ?>,
                lastRow: 'nojustify',
                margins: margin,
                waitThumbnailsLoad: false
            });
        }

        function loadNewImages() {
            justifiedGalleryElement.justifiedGallery('norewind');
        }
    </script>
    <?php
    printPageListWithNav("« " . gettext("prev"), gettext("next") . " »");
    if (function_exists('printAddToFavorites')) printAddToFavorites($_zp_current_album);
    printTags('links', gettext('<strong>Tags:</strong>') . ' ', 'taglist', '');
    callUserFunction('openStreetMap::printOpenStreetMap');
    callUserFunction('printGoogleMap');
    callUserFunction('printSlideShowLink');
    callUserFunction('printRating');
    callUserFunction('printCommentForm');
    ?>
</div>
<?php include 'inc-footer.php'; ?>
</body>
</html>