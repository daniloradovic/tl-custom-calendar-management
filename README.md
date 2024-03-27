# 🎉 TinkerList MVP Calendar Management System 📅

API Events for Calendar Management System MVP.

## Table of Contents 📚

- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Installation 🛠️

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

## 🔌 Usage

1. Start the development server using Laravel Valet:

    ```bash
    composer global require laravel/valet
    valet install
    valet use php@8.2
    cd /Projects
    valet park
    ```

2. Open your browser and visit `http://tl-custom-calendar-management.test` to access the application. 🖥️

3. Refer to the [official documentation](https://laravel.com/docs/11.x/valet) for more details on Valet.

4. The service is hosted on DigitalOcean. You can explore the interactive [API Docs](https://lionfish-app-nzcgq.ondigitalocean.app/api-docs) to access each implemented endpoint. For the MVP, App Platform has been deployed with a basic web service app and a worker for emails.

5. Execute tests with the command 📝:

```bash
./vendor/bin/phpunit
```

For test coverage run:
```bash
./vendor/bin/phpunit --coverage-text --colors=never
```
6. I provided also a quick video walk trough of how to use a interactive api doc for testing endpoints [Part 1](https://www.loom.com/share/2a7ee28e9bb54a84ab7d141543964b7c?sid=58429441-37db-4c43-b707-61f565347a1f) and [Part 2](https://www.loom.com/share/87e62c461f3148ebaeb8d72a54935929?sid=89961f30-632e-46e7-9b14-9d307ea1c79d)

Thanks 😊🎉👨‍💻
