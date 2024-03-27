# TinkerList MVP Calendar Management System

API Events for Calendar Management System MVP.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/daniloradovic/tl-custom-calendar-management.git
    ```

2. Navigate to the project directory:

    ```bash
    cd tl-custom-calendar-management
    ```

3. Install the dependencies:

    ```bash
    php artisan migrate
    composer install
    npm install
    npm run build
    ```

4. To generate emails, run:

    ```bash
    php artisan queue:work
    ```

## Usage

1. Start the development server using Laravel Valet:

    ```bash
    composer global require laravel/valet
    valet install
    valet use php@8.2
    cd /Projects
    valet park
    ```

2. Open your browser and visit `http://tl-custom-calendar-management.test` to access the application.

3. Refer to the [official documentation](https://laravel.com/docs/11.x/valet) for more details on Valet.

4. The service is hosted on DigitalOcean. You can explore the interactive [API Docs](https://lionfish-app-nzcgq.ondigitalocean.app/api-docs) to access each implemented endpoint. For the MVP, App Platform has been deployed with a basic web service app and a worker for emails.

5. Execute tests with the command:

```bash
./vendor/bin/phpunit
