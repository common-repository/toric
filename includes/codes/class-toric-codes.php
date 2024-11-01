<?php
/**
 * Holds Toric_Codes class.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/includes/codes
 */

/**
 * The Toric Codes.
 *
 * @since 1.0.0
 */
class Toric_Codes {

	/**
	 * Holds Toric instance
	 *
	 * @since    1.0.0
	 *
	 * @var Toric
	 */
	protected Toric $toric;

	/**
	 * Constructs Toric_Codes
	 *
	 * @since 1.0.0
	 *
	 * @param  Toric $toric Toric.
	 */
	public function __construct( Toric $toric ) {
		$this->toric = $toric;
	}

	/**
	 * Retrieve QR Code
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value Value.
	 *
	 * @return mixed
	 */
	public function get_qr_code( $value ) {
		// generate a barcode.
		$b_obj = $this->toric->get_barcode()->getBarcodeObj(
			'QRCODE,H',                     // barcode type and additional comma-separated parameters.
			$value,          // data string to encode.
			-8,                             // bar width (use absolute or negative value as multiplication factor).
			-8,                             // bar height (use absolute or negative value as multiplication factor).
			'black',                        // foreground color.
			array( -2, -2, -2, -2 )           // padding (use absolute or negative values as multiplication factors).
		)->setBackgroundColor( 'white' ); // background color.
		//return $b_obj->getHtmlDiv();
		return $b_obj->getSvgCode();
	}

}
