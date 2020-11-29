# TUI Musement API service

Backend microservice that gets the list of the cities from Musement's API for each city gets the forecast for the next 2 days using http://api.weatherapi.com and print to STDOUT "Processed city [city name] | [weather today] - [wheather tomorrow]"

## Installation

Setup instructions are available at [INSTALL.md](INSTALL.md) document.

## Usage

A Symfony command has been created to execute the process. 

```
bin/console app:gets-forecast
```

This command will print the errors in the STDOUT. It also prints the forecast.

## Testing

To execute linters & tests, run:

```
composer lint
composer test:all
```
