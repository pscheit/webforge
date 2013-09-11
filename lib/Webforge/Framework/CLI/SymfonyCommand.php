<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\Command as BaseCommand;
use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\InteractionHelper;

/**
 * Adapter for symfony console commands with CLI-Commands
 */
class SymfonyCommand extends \Webforge\Console\Command {

  protected $cliCommand;

  public function __construct($cliName, ContainerCommand $cliCommand) {
    parent::__construct($cliName);
    $this->cliCommand = $cliCommand;
  }

  protected function doExecute(CommandInput $input, CommandOutput $output, InteractionHelper $interact) {
    $this->cliCommand->initIO($input, $output, $interact);
    
    return $this->cliCommand->executeCLI($input, $output, $interact);
  }
}