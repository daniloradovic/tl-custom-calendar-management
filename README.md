# TinkerList Calendar Management System - MVP

MVP Calendar Management System - API Events.

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

4. For emails to generate, run:

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

2. Open your browser and visit `http://tl-custom-calendar-management.test` to view the application.

3. For more Valet details, check the official documentation [here](https://laravel.com/docs/11.x/valet).

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch.
3. Make your changes.
4. Commit your changes.
5. Push to the branch.
6. Open a pull request.

## License


This project is licensed under the [MIT License](LICENSE).
