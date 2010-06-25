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

  /**
   * @return sfTimer
   */
  private function createTimer()
  {
    $timer = new sfTimer();
    $timer->startTimer();

    return $timer;
  }

  /**
   * @return sfTimer
   */
  private function getTimer()
  {
    $timer = new sfTimer();
    $timer->startTimer();

    return $timer;
  }

  private function showTime(sfTimer $timer, $message = '', $afterMessage = "\n\n")
  {
    $timer->addTime();
    
    echo $message;
    echo (int)$timer->getElapsedTime() . " second(s)\n";
    echo $afterMessage;
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

    $totalTimer = $this->getTimer();

    foreach (file($profile) as $task)
    {

      $timer = $this->getTimer();

      $task = trim($task);

      if(!empty($task))
      {
        $taskName = 'Task: ' . $task;

        $result = 0;
        echo $taskName . "\n\n";
        passthru($task, $result);
        echo "\n";

        $this->showTime($timer);

        if((int)$result > 0)
        {
          $this->showTime($totalTimer, 'Total time: ');
          throw new Exception('`' . $taskName . '`. Running with error #ID: `'.$result.'`');
        }

      }

    }

    $this->showTime($totalTimer, 'Total time: ');

  }
}