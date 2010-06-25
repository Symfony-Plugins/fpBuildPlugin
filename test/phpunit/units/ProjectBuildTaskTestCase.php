<?php

class ProjectBuildTaskTestCase extends sfBasePhpunitTestCase
  implements sfPhpunitFixturePropelAggregator
{    
  protected function _start()
  {
    $test_dir = sfConfig::get('sf_plugin_test_dir').'/temp/'.__CLASS__; 
    file_exists($test_dir) || mkdir($test_dir, 0777, true);
    file_exists($test_dir.'/result') || unlink($test_dir.'/result');
  }
  
  public function testExcecuteWithProfileFileName()
  {
    sfConfig::set('sf_root_dir', $this->fixture()->getDirCommon());
   
    $task = new ProjectBuildTask(new sfEventDispatcher(), new sfFormatter());
    $task->run(array(), array('profile' => 'build.success'));
  }
//  
//  public function testExcecuteStopsOnError()
//  {
//    
//  }
}