<?php
/**
 * Created by PhpStorm.
 * User: amopi
 * Date: 2016-01-10
 * Time: 14:57
 */

namespace Amopi\Mopi;

use Monolog\Logger;
use Amopi\Mlib\Logging\ConsoleHandler;
use Amopi\Mlib\Logging\LocalErrorHandler;
use Amopi\Mlib\Logging\LocalFileHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleApplication extends Application
{
    /** @var Mopi */
    protected $mopi = null;
    
    protected $loggingEnabled = true;
    protected $loggingPath    = null;
    protected $logFilePattern = "%date%/%script%.%command%.%type%";
    protected $loggingLevel   = Logger::DEBUG;
    
    /**
     * @return boolean
     */
    public function isLoggingEnabled()
    {
        return $this->loggingEnabled;
    }
    
    /**
     * @param boolean $loggingEnabled
     */
    public function setLoggingEnabled($loggingEnabled)
    {
        $this->loggingEnabled = $loggingEnabled;
    }
    
    /**
     * @return string
     */
    public function getLogFilePattern()
    {
        return $this->logFilePattern;
    }
    
    /**
     * @param string $logFilePattern
     */
    public function setLogFilePattern($logFilePattern)
    {
        $this->logFilePattern = $logFilePattern;
    }
    
    /**
     * @return int
     */
    public function getLoggingLevel()
    {
        return $this->loggingLevel;
    }
    
    /**
     * @param int $loggingLevel
     */
    public function setLoggingLevel($loggingLevel)
    {
        $this->loggingLevel = $loggingLevel;
    }
    
    /**
     * @return null
     */
    public function getLoggingPath()
    {
        if ($this->loggingPath === null) {
            $this->loggingPath = sys_get_temp_dir() . "/logs";
        }
        
        return $this->loggingPath;
    }
    
    /**
     * @param null $loggingPath
     */
    public function setLoggingPath($loggingPath)
    {
        $this->loggingPath = $loggingPath;
    }
    
    /**
     * @return Mopi
     */
    public function getMopi()
    {
        return $this->mopi;
    }
    
    /**
     * @param Mopi $mopi
     */
    public function setMopi($mopi)
    {
        $this->mopi = $mopi;
    }
    
    protected function configureIO(InputInterface $input, OutputInterface $output)
    {
        parent::configureIO($input, $output);
        
        $level = Logger::DEBUG;
        switch ($output->getVerbosity()) {
            case OutputInterface::VERBOSITY_VERY_VERBOSE:
                $level = Logger::INFO;
                break;
            case OutputInterface::VERBOSITY_VERBOSE:
                $level = Logger::NOTICE;
                break;
            case OutputInterface::VERBOSITY_DEBUG:
                $level = Logger::DEBUG;
                break;
            case OutputInterface::VERBOSITY_NORMAL:
                $level = Logger::WARNING;
                break;
            case OutputInterface::VERBOSITY_QUIET:
                $level = Logger::CRITICAL;
                break;
        }
        if ($this->loggingEnabled) {
            $handler = new ConsoleHandler($level);
            $handler->install();
        }
    }
    
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        if ($this->loggingEnabled) {
            $name             = $command->getName();
            $name             = strtr($name, ":", ".");
            $logFilePattern   = strtr(
                $this->logFilePattern,
                [
                    "%script%" => $name,
                    "%type%"   => "log",
                ]
            );
            $errorFilePattern = strtr(
                $this->logFilePattern,
                [
                    "%script%" => $name,
                    "%type%"   => "error",
                ]
            );
            $logger           = new LocalFileHandler(
                $this->getLoggingPath(), $logFilePattern, $this->getLoggingLevel()
            );
            $logger->install();
            $logger = new LocalErrorHandler(
                $this->getLoggingPath(), $errorFilePattern, $this->getLoggingLevel()
            );
            $logger->install();
        }
        
        return parent::doRunCommand($command, $input, $output);
    }
    
}
