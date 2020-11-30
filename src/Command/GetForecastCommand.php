<?php

declare(strict_types=1);

namespace Tui\Musement\ApiService\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tui\Musement\ApiClient\Domain\City\Model\City;
use Tui\Musement\ApiClient\Infrastructure\Client\Http\HttpClient as MusementApiHttpClient;
use Tui\Weather\ApiClient\Domain\ForecastDay\Model\ForecastDay;
use Tui\Weather\ApiClient\Domain\Response\Model\Response;
use Tui\Weather\ApiClient\Infrastructure\Client\Http\HttpClient as WeatherApiHttpClient;

class GetForecastCommand extends Command
{
    protected const GLUE_TO_SERIALIZE_FORECASTS = ' - ';

    protected const TEMPLATE_TO_SERIALIZE_FORECASTS = 'Processed city %s | %s';

    protected const COMMAND_DESCRIPTION = 'Gets the forecast for a list of cities';

    protected const SUCCESSFUL_EXECUTION_MESSAGE = 'Successful execution!';

    protected const COMMAND_HELP = <<<'EOF'
        The <info>%command.name%</info> command gets the forecast for next 2 days in a city using 
        http://api.weatherapi.com/. This command also uses the list of the cities from Musement's API.
    EOF;
    protected static $defaultName = 'app:gets-forecast';

    /**
     * @var MusementApiHttpClient
     */
    protected $musementApiClient;

    /**
     * @var WeatherApiHttpClient
     */
    protected $weatherApiClient;

    public function __construct(MusementApiHttpClient $musementApiClient, WeatherApiHttpClient $weatherApiClient)
    {
        parent::__construct();

        $this->musementApiClient = $musementApiClient;
        $this->weatherApiClient = $weatherApiClient;
    }

    protected function configure(): void
    {
        $this->setHelp(self::COMMAND_HELP);
        $this->setDescription(self::COMMAND_DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOutputHandler = new SymfonyStyle($input, $output);

        try {
            /** @var City[] $cities */
            $cities = $this->musementApiClient->getCities();
            $this->getSerializedForecasts($inputOutputHandler, $cities);
        } catch (\Exception $exception) {
            $inputOutputHandler->error($exception->getMessage());
            return Command::FAILURE;
        }

        $inputOutputHandler->success(self::SUCCESSFUL_EXECUTION_MESSAGE);

        return Command::SUCCESS;
    }

    /**
     * @param SymfonyStyle $inputOutputHandler
     * @param City[] $cities
     * @throws \Exception
     */
    protected function getSerializedForecasts(SymfonyStyle $inputOutputHandler, array $cities): void
    {
        foreach ($cities as $city) {
            /** @var Response $response */
            $response = $this->weatherApiClient->getForecast($city->latitude(), $city->longitude());
            $forecasts = $response->forecast()->forecastday();
            $formattedForecasts = $this->formatForecasts($forecasts);
            $serializedForecasts = implode(self::GLUE_TO_SERIALIZE_FORECASTS, $formattedForecasts);
            $serializedForecast = sprintf(self::TEMPLATE_TO_SERIALIZE_FORECASTS, $city->name(), $serializedForecasts);

            $inputOutputHandler->text($serializedForecast);
        }
    }

    /**
     * @param ForecastDay[] $forecasts
     * @return string[]
     */
    protected function formatForecasts(array $forecasts): array
    {
        return array_map(function ($forecast) {
            return $forecast->day()->condition()->text();
        }, $forecasts);
    }
}
