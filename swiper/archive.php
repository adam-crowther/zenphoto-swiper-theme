<?php
// force UTF-8 Ø

if (!defined('WEBPATH'))
	die();


/**
 * Retrieves a list of all unique years & months from the images in the gallery
 *
 * @param string $order set to 'desc' for the list to be in descending order
 * @return array
 */
function getAllDays($order = 'asc') {
	$alldates = array();
	$cleandates = array();
	$sql = "SELECT `date` FROM " . prefix('images');
	if (!zp_loggedin()) {
		$sql .= " WHERE `show` = 1";
	}
	$hidealbums = getNotViewableAlbums();
	if (!is_null($hidealbums)) {
		if (zp_loggedin()) {
			$sql .= ' WHERE ';
		} else {
			$sql .= ' AND ';
		}
		foreach ($hidealbums as $id) {
			$sql .= '`albumid`!=' . $id . ' AND ';
		}
		$sql = substr($sql, 0, -5);
	}
	$result = query($sql);
	if ($result) {
		while ($row = db_fetch_assoc($result)) {
			$alldates[] = $row['date'];
		}
		db_free_result($result);
	}
	foreach ($alldates as $adate) {
		if (!empty($adate)) {
			$cleandates[] = substr($adate, 0, 10);
		}
	}
	$datecount = array_count_values($cleandates);
	if ($order == 'desc') {
		krsort($datecount);
	} else {
		ksort($datecount);
	}
	return $datecount;
}

/**
 * Prints a compendum of dates and links to a search page that will show results of the date
 *
 * @param string $class optional class
 * @param string $yearid optional class for "year"
 * @param string $monthid optional class for "month"
 * @param string $order set to 'desc' for the list to be in descending order
 */
function printAllDatesAndMonths($class = 'archive', $yearid = 'year', $monthid = 'month', $order = 'asc') {
	global $_zp_current_search, $_zp_gallery_page;
	if (empty($class)) {
		$classactive = 'archive_active';
	} else {
		$classactive = $class . '_active';
		$class = 'class="' . $class . '"';
	}
	if ($_zp_gallery_page == 'search.php') {
		$activedate = getSearchDate('%Y-%m');
	} else {
		$activedate = '';
	}
	if (!empty($yearid)) {
		$yearid = 'class="' . $yearid . '"';
	}
	if (!empty($monthid)) {
		$monthid = 'class="' . $monthid . '"';
	}
	$datecount = getAllDays($order);
	$lastyear = "";
	echo "\n<ul $class>\n";
	$nr = 0;
	foreach($datecount as $key => $val) {
		$nr++;
		if ($key == '0000-00-01') {
			$year = "no date";
			$month = "";
			$day = "";
		} else {
			$time = strtotime($key);
			$year = strftime('%Y', $time);
			$month = strftime('%B', $time);
			$day = strftime('%d', $time);
		}

		if ($lastyear != $year) {
			$lastyear = $year;
			if ($nr != 1) {
				echo "</ul>\n</li>\n";
			}
			echo "<li $yearid>$year\n<ul $monthid>\n";
		}
		if (is_object($_zp_current_search)) {
			$albumlist = $_zp_current_search->getAlbumList();
		} else {
			$albumlist = NULL;
		}
		$datekey = substr($key, 0, 10);
		if ($activedate = $datekey) {
			$cl = ' class="' . $classactive . '"';
		} else {
			$cl = '';
		}
		echo '<li' . $cl . '><a href="' . html_encode(getSearchURL('', $datekey, '', 0, array('albums' => $albumlist))) . '">' . $day . ' ' . $month . ' (' . $val . ')</a></li>' . "\n";
	}
	echo "</ul>\n</li>\n</ul>\n";
}


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
		<div id="main">
			<div id="gallerytitle">
				<?php
				if(!zp_loggedin()) {
                    $currentUrl = getCustomPageURL('archive');
                    $loginUrl = getCustomPageURL('password', 'from=' . $currentUrl);
                    $loginlink = zp_apply_filter('login_link', $loginUrl);
					$logintext = gettext('Login');
				?>
					<div id="login">
					    <input type="button" 
								class="button buttons"
								onclick="location.href='<?= $loginlink ?>'"
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
						<?php printHomeLink('', ' | '); printGalleryIndexURL(' | ', getGalleryTitle()); ?>
					</span>
					<?php echo gettext("Archive View"); ?>
				</h2>
			</div>
			<div id="padbox">
				<div id="archive"><?php printAllDatesAndMonths('archive', 'year', 'month', 'desc'); ?></div>
				<div id="tag_cloud">
					<p><?php echo gettext('Popular Tags'); ?></p>
					<?php printAllTagsAs('cloud', 'tags'); ?>
				</div>
			</div>
		</div>
		<?php include 'inc-footer.php'; ?>
	</body>
</html>
