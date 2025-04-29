# Project Documentation

## Requirements

- **PHP** 7.4 or higher
- **Composer**
- **PHPUnit** (installed via Composer)

## Installation

1. **Clone the repository** (if you haven’t already):

   ```bash
   git clone https://github.com/your/repository.git
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


