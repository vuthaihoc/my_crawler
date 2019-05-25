<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 5/24/19
 * Time: 11:34
 */

namespace MyCrawler\Demo4;


use GuzzleHttp\Psr7\Uri;

class MyUri extends Uri {
	
	protected $step;
	
	/**
	 * MyUri constructor.
	 *
	 * @param $step
	 */
	public function __construct( $uri = '', $step = '' ) {
		$this->step = $step;
		parent::__construct($uri);
	}
	
	/**
	 * @return string
	 */
	public function getStep() {
		return $this->step;
	}
	
	/**
	 * @param string $step
	 */
	public function setStep( $step ) {
		$this->step = $step;
	}
	
}