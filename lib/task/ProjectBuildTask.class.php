<?php

class ProjectBuildTask extends sfBaseTask
{
  protected $_totalTimer;

  protected $_quiet = false;

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
    sfCommandArgument::REQUIRED,
      'Build profile')));

    // init total timer
    $this->_totalTimer = $this->getTimer();

    chdir(sfConfig::get('sf_root_dir'));
  }


  protected function execute($arguments = array(), $options = array())
  {
    if(isset($options['quiet']))
    {
      $this->_quiet = true;
    }

    foreach ($this->_prepareCommands($arguments['profile']) as $command) {
      $this->_doCommand($command);
    }

    $this->showTime($this->getTotalTimmer(), 'Total time: ');
  }

  protected function _doCommand($command)
  {
    $command = trim($command);
    if(empty($command)) return;

    $this->logSection("Command :", $command);

    $timer = $this->getTimer();

    $this->_call($command);

    $this->showTime($timer);

  }

  public function _call($command)
  {
    $result = 0;
    $message = '';
    
    if($this->_quiet)
    {
      ob_start();
    }

    passthru($command, $result);

    if($this->_quiet)
    {
      $message = ob_get_contents();
      ob_end_clean();
    }

    if((int)$result > 0)
    {
      $this->showTime($this->getTotalTimmer(), 'Total time: ');

      throw new Exception('Command: `' . $command . '`. Exit with error code: `'.$result.'`. ' . $message);
    }

    return $result;

  }

  /**
   *
   * @param string $source
   *
   * @throws Exceptino if the source cannot be found.
   *
   * @return array
   */
  protected function _prepareCommands($source)
  {
    // guess file form project root config directory.
    $relativeConfigPath = sfConfig::get('sf_root_dir').'/config/'.$source;
    if (file_exists($relativeConfigPath)) {
      return file($relativeConfigPath);
    }

    // guess relative from project root
    $relativeRootPath = sfConfig::get('sf_root_dir') . '/'. $source;
    if (file_exists($relativeRootPath)) {
      return file($relativeRootPath);
    }

    // guess absolute
    $absolutePath = $source;
    if (file_exists($absolutePath)) {
      return file($absolutePath);
    }

    throw new Exception("Provided path to build profile is not exist or invalid. Given parameter is `{$source}`. The next pathes were tried: " .
      "\nRelative from config `{$relativeConfigPath}`," .
      "\nRelative from root - `{$relativeRootPath}`," .
      "\nAbsolute - `{$absolutePath}`");
  }

  /**
   * @return sfTimer
   */
  protected function getTimer()
  {
    $timer = new sfTimer();
    $timer->startTimer();

    return $timer;
  }

  /**
   * @return sfTimer
   */
  protected function getTotalTimmer()
  {
    return $this->_totalTimer;
  }

  protected function showTime(sfTimer $timer, $message = 'Time : ', $afterMessage = "\n\n")
  {
    $timer->addTime();
    $this->log('');
    $this->logSection($message, date('i:s', (int) $timer->getElapsedTime()));
    $this->log('');
    $this->log('');
  }
}