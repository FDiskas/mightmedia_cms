<?php
//todo: remove
$sql_p = mysql_query1( "SELECT * FROM `" . LENTELES_PRIESAGA . "panel` WHERE `align`='L' AND `lang` = " . escape( lang() ) . " ORDER BY `place` ASC", 120 );

foreach ( $sql_p as $row_p ) {
	if ( teises( $row_p['teises'], $_SESSION[SLAPTAS]['level'] ) ) {
		//todo: after v2 optimize it
		if(is_file($row_p['file'])) {
			$includeBlock = $row_p['file'];
		} elseif(is_file("blokai/" . basename($row_p['file']))) {
			$includeBlock = "blokai/" . basename($row_p['file']);
		} else {
			$includeBlock = null;
		}

		if (! empty($includeBlock)) {
			
			include_once $includeBlock;

			if ( empty( $title ) ) {
				$title = $row_p['panel'];
			}
			if ( $row_p['show'] == 'Y' && isset( $text ) && !empty( $text ) && isset( $_SESSION[SLAPTAS]['level'] ) && teises( $row_p['teises'], $_SESSION[SLAPTAS]['level'] ) ) {
				lentele_l( $title, $text );
				unset( $title, $text );
			} elseif ( isset( $text ) && !empty( $text ) && $row_p['show'] == 'N' && isset( $_SESSION[SLAPTAS]['level'] ) && teises( $row_p['teises'], $_SESSION[SLAPTAS]['level'] ) ) {
				echo $text;
				unset( $text, $title );
			} else {
				unset( $text, $title );
			}
		} else {
			echo lentele_l( $lang['system']['error'], $lang['system']['nopanel'] . ", " . $row_p['file'] );
		}
	}
}
unset( $sql_p, $row_p, $title, $text );