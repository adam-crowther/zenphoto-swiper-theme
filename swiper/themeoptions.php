<?php

// force UTF-8 Ø

/* Plug-in for theme option handling
 * The Admin Options page tests for the presence of this file in a theme folder
 * If it is present it is linked to with a require_once call.
 * If it is not present, no theme options are displayed.
 *
 */

require_once(dirname(__FILE__) . '/functions.php');

class ThemeOptions {

	function __construct() {
		$me = basename(dirname(__FILE__));
		setThemeOptionDefault('Allow_search', true);
		setThemeOptionDefault('Theme_colors', 'light');
		setThemeOptionDefault('Album_passepartout_border', 20);
		setThemeOptionDefault('Image_passepartout_border', 20);
		setThemeOptionDefault('Gallery_margin', 55);
		setThemeOptionDefault('albums_per_page', 10000);
		setThemeOptionDefault('albums_per_row', 10000);
		setThemeOptionDefault('images_per_page', 10000);
		setThemeOptionDefault('images_per_row', 10000);
		setThemeOptionDefault('image_size', 595);
		setThemeOptionDefault('image_use_side', 'longest');
		setThemeOptionDefault('thumb_size', 140);
        setThemeOptionDefault('thumb_use_side', 'height');
		setThemeOptionDefault('thumb_crop_width', 140);
		setThemeOptionDefault('thumb_crop_height', 140);
		setThemeOptionDefault('thumb_crop', 0);
		setThemeOptionDefault('thumb_transition', 0);
		setOptionDefault('colorbox_' . $me . '_album', 1);
		setOptionDefault('colorbox_' . $me . '_image', 1);
		setOptionDefault('colorbox_' . $me . '_search', 1);
		if (class_exists('cacheManager')) {
			cacheManager::deleteCacheSizes($me);
			cacheManager::addDefaultThumbSize();
			cacheManager::addDefaultSizedImageSize();
			cacheManager::addCacheSize($me, 200, 80, 160, 80, 160, null, null, true, false);
		}
	}

	function getOptionsSupported() {
		return array(gettext('Allow search') => array(
						'key' => 'Allow_search',
						'type' => OPTION_TYPE_CHECKBOX,
						'desc' => gettext('Check to enable search form.')),
				gettext('Theme colors') => array(
						'key' => 'Theme_colors',
						'type' => OPTION_TYPE_CUSTOM,
						'desc' => gettext('Select the colors of the theme.')),
                gettext('Album Passepartout width') => array(
                    'key' => 'Album_passepartout_border',
                    'type' => OPTION_TYPE_CUSTOM,
                    'desc' => gettext('The width of the passepartout border around the album thumbnails in px.')),
                gettext('Image Passepartout width') => array(
                    'key' => 'Image_passepartout_border',
                    'type' => OPTION_TYPE_CUSTOM,
                    'desc' => gettext('The width of the passepartout border around the image in px.')),
                gettext('Gallery margin') => array(
                    'key' => 'Gallery_margin',
                    'type' => OPTION_TYPE_CUSTOM,
                    'desc' => gettext('The width of the margins between the album thumbnails in px.'))
		);
	}

	function getOptionsDisabled() {
		return array('custom_index_page');
	}

	function handleOption($option, $currentValue) {
		global $themecolors;
		if ($option == 'Theme_colors') {
			echo '<select id="EF_themeselect_colors" name="' . $option . '"' . ">\n";
			generateListFromArray(array($currentValue), $themecolors, false, false);
			echo "</select>\n";
		}
		if ($option == 'Album_passepartout_border') {
            echo "<input type='text' size='3' id='EF_album_passepartout_border' name='$option' value='$currentValue'>\n";
		}
		if ($option == 'Image_passepartout_border') {
            echo "<input type='text' size='3' id='EF_image_passepartout_border' name='$option' value='$currentValue'>\n";
		}
		if ($option == 'Gallery_margin') {
            echo "<input type='text' size='3'  id='EF_gallery_margin' name='$option' value='$currentValue'>\n";
		}
	}
}
?>