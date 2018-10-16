<?php
//medzio darymo f-ja
function build_tree( $data, $id = 0, $active_class = 'active' ) {

	global $lang;
	if ( !empty( $data ) ) {
		$re = "";
		foreach ( $data[$id] as $row ) {
			if ( isset( $data[$row['id']] ) ) {
				$re .= "<li><a href=\"" . url( '?id,' . $row['id'] ) . "\" >" . $row['pavadinimas'] . "</a><span style=\"display: inline; width: 100px;margin:0; padding:0; height: 16px;\"><a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';d,' . $row['id'] ) . "\"  onClick=\"return confirm(\'" . $lang['admin']['delete'] . "?\')\"><img src=\"" . ROOT . "images/icons/cross.png\" title=\"" . $lang['admin']['delete'] . "\"  /></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';r,' . $row['id'] ) . "\"><img src=\"" . ROOT . "images/icons/wrench.png\" title=\"" . $lang['admin']['edit'] . "\"/></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';e,' . $row['id'] ) . "\"><img src=\"" . ROOT . "images/icons/pencil.png\" title=\"" . $lang['admin']['page_text'] . "\" /></a></span><ul>";
				$re .= build_tree( $data, $row['id'], $active_class );
				$re .= "</ul></li>";
			} else {
				$re .= "<li><a href=\"" . url( '?id,' . $row['id'] ) . "\" >" . $row['pavadinimas'] . "</a><span style=\"display: inline; width: 100px; margin:0; padding:0; height: 16px;\">
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';d,' . $row['id'] ) . "\" onClick=\"return confirm(\'" . $lang['admin']['delete'] . "?\')\"><img src=\"" . ROOT . "images/icons/cross.png\" title=\"" . $lang['admin']['delete'] . "\"/></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';r,' . $row['id'] ) . "\"><img src=\"" . ROOT . "images/icons/wrench.png\" title=\"" . $lang['admin']['edit'] . "\" /></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';e,' . $row['id'] ) . "\" ><img src=\"" . ROOT . "images/icons/pencil.png\" title=\"" . $lang['admin']['page_text'] . "\" /></a></span>
</li>";
			}
		}
		return $re;
	}
}

function editor( $tipas = 'jquery', $dydis = 'standartinis', $id = FALSE, $value = '' ) {

	global $conf, $lang;

	if ( !$id ) {
		$id = md5( uniqid() );
	}

	if ( is_array( $id ) ) {
		foreach ( $id as $key => $val ) {
			$arr[$val] = "'$key'";
		}
		$areos = implode( $arr, "," );
	} else {
		$areos = "'$id'";
	}

	$root = ROOT;

	if ( $conf['Editor'] == 'textarea' ) {
		$return = '';
		if ( is_array( $id ) ) {
			foreach ( $id as $key => $val ) {
				$return .= <<<HTML

	<textarea id="{$key}" name="{$key}" rows="1" class="form-control no-resize auto-growth">{$value[$key]}</textarea>
HTML;
			}
		} else {
			$return .= <<<HTML

<textarea id="{$id}" name="{$id}" rows="1" class="form-control no-resize auto-growth">{$value}</textarea>
HTML;

		}
	} elseif ( $conf['Editor'] == 'tinymce' ) {
		$dir    = adresas();
		$return = <<<HTML
      <!-- Load TinyMCE -->
<script src="{$dir}htmlarea/tinymce/tinymce.js" type="text/javascript"></script>
<script type="text/javascript">
	//TinyMCE
    tinymce.init({
        selector: "textarea.tinymce",
        theme: "modern",
        height: 300,
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
		image_advtab: true,
		// images_upload_url: 'postAcceptor.php', - images local upload
    });
    // tinymce.suffix = ".min";
    // tinyMCE.baseURL = '{$dir}tinymce';

</script>
<!-- /TinyMCE -->
HTML;
		if ( is_array( $id ) ) {
			foreach ( $id as $key => $val ) {

				$return .= <<< HTML
<textarea id="{$key}" name="{$key}" class="tinymce">{$value[$key]}</textarea>
HTML;
			}
		} else {
			$return .= <<< HTML
<textarea id="{$id}" name="{$id}" class="tinymce">{$value}</textarea>
HTML;

		}
		
	} elseif ( $conf['Editor'] == 'ckeditor' ) {
		$dir = adresas();

		$return = <<<HTML
	<script type="text/javascript" src="{$dir}htmlarea/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		//CKEditor
		CKEDITOR.replaceClass = 'ckeditor';
		CKEDITOR.config.height = 300;
		CKEDITOR.config.extraPlugins = 'uploadimage';
	</script>
HTML;

		if ( is_array( $id ) ) {
			foreach ( $id as $key => $val ) {

				$return .= <<< HTML
<textarea id="{$key}" name="{$key}" class="ckeditor">{$value[$key]}</textarea>
HTML;
			}
		} else {
			$return .= <<< HTML
<textarea id="{$id}" name="{$id}" class="ckeditor">{$value}</textarea>
HTML;

		}
	}
	
	return $return;
}

function defaultHead() 
{
	global $conf;

	?>
	<base href="<?php echo adresas(); ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo input(strip_tags($conf['Pavadinimas']) . ' - Admin'); ?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index,follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	<?php
}

function adminPages() 
{
	global $url, $lang, $conf, $buttons, $adminMenu, $adminExtensionsMenu, $timeout;
	
	$mergedMenus = array_merge($adminMenu, $adminExtensionsMenu);
	$fileName = (isset($url['a']) && isset($mergedMenus[$url['a']] ) ? $mergedMenus[$url['a']] : null);

	if (! empty($fileName) && file_exists(dirname(__DIR__) . "/" . $fileName) && isset($_SESSION[SLAPTAS]['username']) && $_SESSION[SLAPTAS]['level'] == 1 && defined( "OK" ) ) {
		if (count($_POST) > 0 && $conf['keshas'] == 1) {
			msg( $lang['system']['warning'], $lang['system']['cache_info'] );
		}
		
		include_once("/" . $fileName);

	} elseif ( isset( $_GET['m'] ) ) {

		switch ( $_GET['m'] ) {
			case 1:
				$page = 'uncache.php';
				break;
			case 2:
				$page = 'pokalbiai.php';
				break;
			case 3:
				$page = 'antivirus.php';
				break;
			case 4:
				$page = 'search.php';
				break;
		}

		include_once("/" . $page);
	} else {
		include_once("/start.php");
	}
}

function getAdminExtensionsMenu($page = null) 
{
	global $adminExtensionsMenu;

	$menu = event('adminExtensionsMenu', $adminExtensionsMenu);

	return ! empty($page) ? $menu[$page] : $menu;
}

function getAdminPages($page = null) 
{
	global $adminMenu;

	$menu = event('adminPages', $adminMenu);

	return ! empty($page) ? $menu[$page] : $menu;
}

function getAdminPagesbyId($id = null) 
{
	global $adminMenu;

	$menu = getAdminPages();
	$menu = array_flip($menu);

	return ! empty($id) ? $menu[$id . '.php'] : $menu;
}

//default hooks
event('adminPages', NULL, function($menu) {

	return $menu;
});

event('adminExtensionsMenu', NULL, function($menu) {

	return $menu;
});

function getFeedArray($feedUrl) 
{
     
    $content = file_get_contents($feedUrl);
	$x = simplexml_load_string($content, null, LIBXML_NOCDATA);
	
    return $x->channel;
}

function blocksOrder($data) 
{
	global $lang;

	if ( isset( $data['order'] ) ) {
		$array = json_decode($data['order'], true);
		$case_place = '';
		$where = '';
		foreach ($array as $position => $item) {
			$case_place .= "WHEN " . (int)$item['id'] . " THEN '" . (int)$position . "' ";
	
			$where .= $item['id'] . ",";
		}
		$where = rtrim($where, ", ");
		$sqlas = "UPDATE `" . LENTELES_PRIESAGA . "panel` SET `place`= (CASE id " . $case_place . " END) WHERE id IN (" . $where . ")";

		if($result = mysql_query1($sqlas)) {
			delete_cache( "SELECT * FROM `" . LENTELES_PRIESAGA . "panel` WHERE `align`='R' AND `lang` = " . escape( lang() ) . " ORDER BY `place` ASC" );
			delete_cache( "SELECT * FROM `" . LENTELES_PRIESAGA . "panel` WHERE `align`='L' AND `lang` = " . escape( lang() ) . " ORDER BY `place` ASC" );
			delete_cache( "SELECT * FROM `" . LENTELES_PRIESAGA . "panel` WHERE `align`='C' AND `lang` = " . escape( lang() ) . " ORDER BY `place` ASC" );

			return $lang['system']['updated'];
		}
		
		return null;
	}
}