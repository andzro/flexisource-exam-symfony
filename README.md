# Symfony Project for Flexisource Exam

## Table of Contents

- [Project Overview](#project-overview)
- [Dependencies](#dependencies)
- [Available Endpoints](#available-endpoints)
- [Requirements](#requirements)
- [Setup Instructions](#setup-instructions)
- [Running the Application](#running-the-application)
- [Database Migrations](#database-migrations)
- [Importing Customers](#importing-customers)
- [Testing](#testing)
- [Room for Improvements](#room-for-improvements)

## Overview

This Symfony project is based on the Flexisource exam provided. It includes various components such as customer management, an import command, and API endpoints for customer data retrieval.

## Dependencies

This project uses the following dependencies:

```json
{
    "type": "project",
    "license": "proprietary",
    "minimum-stability": "stable",
    "prefer-stable": true,
    "require": {
        "php": ">=8.2",
        "ext-ctype": "*",
        "ext-iconv": "*",
        "doctrine/dbal": "^3",
        "doctrine/doctrine-bundle": "^2.12",
        "doctrine/doctrine-migrations-bundle": "^3.3",
        "doctrine/orm": "^3.2",
        "symfony/console": "7.1.*",
        "symfony/dotenv": "7.1.*",
        "symfony/flex": "^2",
        "symfony/framework-bundle": "7.1.*",
        "symfony/http-client": "7.1.*",
        "symfony/runtime": "7.1.*",
        "symfony/yaml": "7.1.*"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "symfony/browser-kit": "7.1.*",
        "symfony/css-selector": "7.1.*",
        "symfony/maker-bundle": "^1.60",
        "symfony/phpunit-bridge": "^7.1"
    }
}
```

## Endpoints Available

- `/customers` - GET endpoint that returns a list of customers.
- `/customers/{id}` - GET endpoint that returns customer data by ID.

## Requirements

- Composer
- PHP 8
- Docker (for DB) or a running DB instance

## How to Run

### Local Setup

1. Install dependencies:
    ```bash
    composer install
    ```

2. Set up the Docker database using `docker-compose.yml` or configure a local database by updating the `.env` file with the appropriate DB credentials.

3. Run the Symfony server:
    ```bash
    php -S 127.0.0.1:<port> -t public
    ```

### Migrations

To run the migrations:
```bash
bin/console doctrine:migrations:migrate
```
**Note:** Ensure that your database configuration in the `.env` file is correct before running this command.

### Importing Customers

An import command is available to fetch customer data from an external API. By default, it imports from `https://randomuser.me/api`.

To run the import command:
```bash
bin/console app:import-customer
```

If you want to use a different URL, you can provide it as an argument:
```bash
bin/console app:import-customer <url>
```

Example:
```bash
bin/console app:import-customer https://randomuser.me/api?results=100&nat=BR
```

## Testing

Testing is implemented using PHPUnit and Symfony's testing tools. To run tests, use:

```bash
bin/phpunit
```

This command will execute the test suite and provide feedback on your code's correctness.


## Room for Improvements

Due to limited time, the following improvements were considered but not implemented. If you are interested in enhancing this project, feel free to address these areas:

1. **Dynamic Import Script:** The current import script is retrofitted to use only the RandomUser API. Ideally, it should be dynamic to handle various data structures and sources.

2. **Entity Validations:** Additional validations could be implemented, such as email format checks, phone number format validations, etc.

3. **Auto-Generated API Documentation:** Implementing tools like Swagger-UI for auto-generated API documentation would enhance the project's usability and appeal.

4. **API Versioning:** While not requested, API versioning could be a valuable feature for maintaining backward compatibility.

5. **Enhanced Error Handling:** More comprehensive error handling and logging mechanisms could improve robustness and maintainability.

6. **Unit and Integration Testing:** Expanding the test coverage to include more edge cases and scenarios could help ensure reliability.

7. **Configuration Management:** Improving configuration management, such as parameterizing different environments (development, staging, production), could enhance deployment flexibility.

8. **Containerized Docker Setup:** Currently, only the database is set up with Docker. It would be beneficial to containerize the entire application, including the Symfony application, to ensure consistent environments and simplify deployment. A Dockerfile and `docker-compose.yml` for the Symfony application could be added for a complete setup.

---

Feel free to use this README.md as a reference or contribute to these improvements.