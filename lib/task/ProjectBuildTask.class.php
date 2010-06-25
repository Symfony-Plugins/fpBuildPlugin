<?php

class ProjectBuildTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace        = 'project';
    $this->name             = 'build';
    $this->briefDescription = 'Build project';

    $this->detailedDescription = <<<EOF
Build Project
EOF;

    $this->addArguments(array(
    new sfCommandArgument(
      'profile',
      sfCommandArgument::OPTIONAL, 
      'Build profile')));

  }

  protected function execute($arguments = array(), $options = array())
  {
    $profile = $arguments['profile'];
    $configProfile = sfConfig::get('sf_root_dir').'/config/'.$profile;
    if (file_exists($configProfile)) {
      $profile = $configProfile;
    }
    if (!(file_exists($profile) && is_readable($profile))) {
      throw new Exception('Provided path to build profile is not exist or invalid. Given path is `'.$arguments['profile'].'`');
    }

    chdir(sfConfig::get('sf_root_dir'));
    foreach (file($profile) as $task) {
      $this->log($this->getFilesystem()->sh($task));
    }
  }
}