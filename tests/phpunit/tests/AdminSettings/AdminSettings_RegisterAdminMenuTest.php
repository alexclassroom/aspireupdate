<?php
/**
 * Class AdminSettings_RegisterAdminMenuTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Admin_Settings::register_admin_menu()
 *
 * These tests cause constants to be defined.
 * They must run in separate processes and must not preserve global state.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @covers \AspireUpdate\Admin_Settings::register_admin_menu
 */
class AdminSettings_RegisterAdminMenuTest extends AdminSettings_UnitTestCase {

	/**
	 * Test that menu is not registered when AP_REMOVE_UI is enabled.
	 */
	public function test_should_not_register_menu_when_ap_remove_ui_is_enabled() {
		global $submenu;
		$original_submenu = $submenu;

		define( 'AP_REMOVE_UI', true );

		$admin_settings = new \AspireUpdate\Admin_Settings();
		$admin_settings->register_admin_menu();

		$this->assertSame( $original_submenu, $submenu );
	}

	/**
	 * Test that menu is not registered when the user lacks appropriate permissions.
	 */
	public function test_should_not_register_menu_when_user_lacks_appropriate_permissions() {
		global $submenu;
		$original_submenu = $submenu;

		define( 'AP_REMOVE_UI', false );
		wp_set_current_user( self::$editor_id );

		$admin_settings = new \AspireUpdate\Admin_Settings();
		$admin_settings->register_admin_menu();

		$this->assertSame( $original_submenu, $submenu );
	}

	/**
	 * Test that menu is registered when AP_REMOVE_UI is disabled.
	 */
	public function test_should_register_menu_when_ap_remove_ui_is_disabled() {
		global $submenu;

		define( 'AP_REMOVE_UI', false );
		grant_super_admin( self::$admin_id );

		$admin_settings = new \AspireUpdate\Admin_Settings();
		$admin_settings->register_admin_menu();

		$this->assertIsArray(
			$submenu,
			'There are no submenus.'
		);

		$base = is_multisite() ? 'settings.php' : 'options-general.php';
		$this->assertArrayHasKey(
			$base,
			$submenu,
			'There is no dashboard section.'
		);

		$this->assertIsArray(
			$submenu[ $base ],
			'There are no submenus for the dashboard.'
		);

		$last_menu_item = end( $submenu[ $base ] );
		$this->assertSame(
			'aspireupdate-settings',
			$last_menu_item[2],
			'The menu was not registered.'
		);
	}

	/**
	 * Test that AP_REMOVE_UI is defined when not already defined.
	 */
	public function test_should_define_ap_remove_ui_when_not_already_defined() {
		$this->assertFalse(
			defined( 'AP_REMOVE_UI' ),
			'AP_REMOVE_UI is already defined.'
		);

		$admin_settings = new AspireUpdate\Admin_Settings();
		$admin_settings->register_admin_menu();

		$this->assertTrue(
			defined( 'AP_REMOVE_UI' ),
			'AP_REMOVE_UI is not defined.'
		);

		$this->assertFalse(
			AP_REMOVE_UI,
			'AP_REMOVE_UI is not defined as (bool) false.'
		);
	}
}
