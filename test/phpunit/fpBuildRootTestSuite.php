<?php

class fpBuildRootTestSuite extends sfBasePhpunitTestSuite
{
	/**
	 * Dev hook for custom "setUp" stuff
	 */
	protected function _start()
	{
	  sfConfig::set('sf_plugin_test_dir', dirname(__FILE__));
	}
	
	protected function _end()
	{
	}
}