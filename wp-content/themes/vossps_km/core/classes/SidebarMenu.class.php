<?php


namespace Lumi\Classes;



class SidebarMenu {

	public static function getMenu(){

		global $lumi_sidebar_menu;

		if( !isset( $lumi_sidebar_menu ) ) {
			$lumi_sidebar_menu = new SidebarMenuAPI();
		}

		return $lumi_sidebar_menu;

	}

}