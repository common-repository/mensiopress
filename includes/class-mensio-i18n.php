<?php
class mensio_i18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'mensio',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
