# Project Documentation

## Requirements

- **PHP** 7.4 or higher
- **Composer**
- **PHPUnit** (installed via Composer)

## Installation

1. **Clone the repository** (if you haven’t already):

   ```bash
   git clone https://github.com/meylisday/commission-calculator.git
   cd your-repository
   ```

2. **Install project dependencies** with Composer:

   ```bash
   composer install
   ```

   This will install all libraries defined in `composer.json`.

## Usage

To run the application against a CSV input file, use:

```bash
php bin/console.php input.csv
```

- `input.csv` should contain your operations in the expected format: `date, user_id, user_type, operation_type, amount, currency`.

## Running Tests

After installing dependencies, you can execute the test suite with PHPUnit:

```bash
./vendor/bin/phpunit
```

This command will run all test cases in the `tests/` directory and output the results.

## Time Spent

The total time spent on this task was approximately **6 hours**, including:

- Reviewing the task requirements
- Developing the functionality
- Writing and running tests
- Refactoring the code
- Deployment and documentation

## Next Steps

Here are some improvements and further development steps I would consider:

- Implement dedicated strategy classes for **Business** and **Private** clients to follow the Open/Closed Principle.
- Extract configuration values (such as commission rates, currency decimal rules, and limits) into a separate **config file** for better flexibility and maintainability.
- Improve error handling and add logging for better observability.
- Add support for additional currencies dynamically from an external source.
- Introduce interface contracts for commission strategies to make the system more extensible.
- Enhance unit test coverage and include edge case scenarios.


