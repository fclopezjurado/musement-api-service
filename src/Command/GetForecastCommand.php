<?php

declare(strict_types=1);

namespace Tui\Musement\ApiService\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tui\Musement\ApiClient\Infrastructure\Client\Http\HttpClient;

class GetForecastCommand extends Command
{
    protected const COMMAND_DESCRIPTION = 'Gets the forecast for a list of cities';

    protected const COMMAND_HELP = <<<'EOF'
        The <info>%command.name%</info> command gets the forecast for next 2 days in a city using 
        http://api.weatherapi.com/. This command also uses the list of the cities from Musement's API.
    EOF;
    protected static $defaultName = 'app:gets-forecast';

    /**
     * @var HttpClient
     */
    protected $client;

    public function __construct(HttpClient $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    protected function configure(): void
    {
        $this->setHelp(self::COMMAND_HELP);
        $this->setDescription(self::COMMAND_DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOutputHandler = new SymfonyStyle($input, $output);

        print_r($this->client->getCities());

        $inputOutputHandler->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
