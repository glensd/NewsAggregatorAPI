# News Aggregator API

## Overview
The News Aggregator API is a backend service built with Laravel that allows users to access news articles from various sources, personalized according to their preferences. This API fetches articles from multiple news APIs, including NewsAPI, The Guardian, and The New York Times.

## Table of Contents
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Requirements](#requirements)
- [Installation](#installation)
- [Docker](#docker)
- [API Endpoints](#api-endpoints)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [License](#license)

## Screenshots
### Swagger Documentation Screenshots
    <div>
    <img alt="Screenshot 2024-10-29 at 1 13 09 PM" src="https://github.com/user-attachments/assets/9d94b3f2-f76c-4991-bf90-4618f25cd7d8" width="20%"></img> 
    <img alt="Screenshot 2024-10-29 at 1 10 29 PM" src="https://github.com/user-attachments/assets/f27bde6c-72c4-41a4-9aa9-6073b4f5c4b8" width="20%"></img> 
    <img alt="Screenshot 2024-10-29 at 1 10 51 PM" src="https://github.com/user-attachments/assets/3d5f9d56-b1ea-4d9e-9249-c24da371b158" width="20%"></img> 
    <img alt="Screenshot 2024-10-29 at 1 11 37 PM" src="https://github.com/user-attachments/assets/f6e1652b-8d78-48c1-beaf-47f56574ea77" width="20%"></img> 
    <img alt="Screenshot 2024-10-29 at 1 12 05 PM" src="https://github.com/user-attachments/assets/a04bc3e9-9f92-4d0c-9c54-2ff1e051029e" width="20%"></img> 
</div>
  


## Features
- User authentication (registration, login, logout, password reset)
- Article management (fetch, store, update, and retrieve articles)
- User preferences management (set and retrieve preferred news sources, categories, and authors)
- Personalized news feed based on user preferences
- Get hourly news from NewsAPI, The Guardian, and The New York Times using command.

## Technologies Used
- Laravel 10
- MySQL
- HTTP Client (for API requests)
- Caching (for optimized performance)
- Swagger/OpenAPI (for API documentation)

## Requirements
- PHP 8.0 or higher
- Composer
- Laravel 8 or higher
- MySQL or any compatible database

## Installation
1. Clone the repository:
   ```bash
   git clone <repository-url>
   ```
   
2. Navigate into the project directory:
    ```bash
   cd <project-directory>
   ```
   
3. Install dependencies:
    ```bash
    composer install
    ```
   
4. Set up your .env file:
    ```bash
    cp .env.example .env
    ```
   
5. Generate an application key:
    ```bash
    php artisan key:generate
    ```
   
6. Run migrations to set up the database:
    ``` bash 
    php artisan migrate
    ```
7. Seed the database :
    ```bash
    php artisan db:seed
    ```
   
8. Start the local development server:
    ```bash
    php artisan serve
    ```

## Docker
This project can also be run using Docker. Follow these instructions:

-   Make sure Docker is installed and running on your machine.
-   Create a docker-compose.yml file in the root of the project with the following content:

```bash
    version: '3.8'

    services:
        app:
            image: php:8.1-fpm
            container_name: newsaggregator_app
            restart: unless-stopped
            working_dir: /var/www/html
            volumes:
                - ./:/var/www/html
            depends_on:
                - db
            command: php -S 0.0.0.0:8000 -t public
    
        db:
            image: mysql:latest
            container_name: newsaggregator_db
            restart: unless-stopped
            environment:
                MYSQL_ROOT_PASSWORD: root
                MYSQL_DATABASE: news_aggregator_api
                MYSQL_USER: newsAggreagtor
                MYSQL_PASSWORD: newsAggreagtor@123
            volumes:
                - db_data:/var/lib/mysql
    
    volumes:
        db_data:
    
    networks:
        newsaggregator_network:
            driver: bridge
```

-   Build and start the containers:
```bash
  docker-compose up -d 
```
-   Access the application at http://localhost:8000

### Database Credentials
Use the following credentials to connect to the MySQL database:

-   DB_CONNECTION: mysql
-   DB_HOST: db (Docker service name)
-   DB_PORT: 3306
-   DB_DATABASE: news_aggregator_api
-   DB_USERNAME: newsAggreagtor
-   DB_PASSWORD: newsAggreagtor@123

## Fetching Articles
To regularly fetch and store articles from news APIs, run the following command:
``` bash
php artisan articles:fetch
```
This command fetches articles from multiple news sources, including
-   NewsAPI
- The Guardian
- The New York Times

It retrieves relevant articles and stores them in the database for your application.

## API Endpoints
### Authentication

#### User registration
- POST /api/register

#### User login
- POST /api/login

#### User logout
- POST /api/logout
    
#### Reset Password
- POST /api/forgot-password
- POST /api/reset-password

### Article Management
#### Retrieve articles with filters (title, content, source, date, category)
- GET /api/articles

####  Retrieve a specific article
-   GET /api/articles/{id}

#### Retrieve personalized news feed based on user preferences
- GET /api/user-personalized-feed/{user_id}

### User Preferences
#### Set user preferences (news sources, categories, authors)
-  POST /api/preferences

#### Retrieve user preferences
- GET /api/preferences/{userId}


## API Documentation
Comprehensive API documentation is available using Swagger/OpenAPI. To access it, run the following command:
``` bash
php artisan l5-swagger:generate
```
After running the command, navigate to the following URL to access the Swagger API documentation:

-   http://localhost:8000/api/documentation

### You can also view the custom API documentation by clicking on the link below:

- [Custom API Documentation](https://glensd.github.io/newsaggregator-api/)

## Email Testing
For testing email functionality, including the forgot password feature, the application uses **Mailtrap**.
Ensure your Mailtrap credentials are set in the .env file.

## Testing
### Summary of Test Cases
Below is a summary of the API test cases and their status:
####    Registration (/api/register)
  - Test case: Successful user registration
  - Status: Passed ✅

#### Login (/api/login)
  - Test case: Successful login with valid credentials
  - Status: Passed ✅
  - Test case: Attempt to login with non-existent email
  - Status: Passed ✅
  - Test case: Attempt to login with incorrect password
  - Status: Passed ✅
  - Test case: Attempt to login without password
  - Status: Passed ✅

#### Forgot Password (/api/forgot-password)
- Test case: Email exists, reset link sent
- Status: Passed ✅ 
- Test case: Email does not exist, validation error
- Status: Passed ✅


#### Reset Password (/api/reset-password)

- Test case: Valid token and new password provided
- Status: Passed ✅
- Test case: Invalid token, unable to reset password
- Status: Passed ✅


####  Logout (/api/logout)
- Test case: Successful logout with email provided
- Status: Passed ✅ 
- Test case: Attempt to logout with invalid email
- Status: Passed ✅

###  Categories API
####    Get Categories (/api/categories)
* Test case: Successfully retrieves categories
* Status: Passed ✅

###  Articles API
####   Show Article (/api/articles/{id})
* Test case: Successfully retrieves an article by ID
* Status: Passed ✅

####  Index Articles (/api/articles)
* Test case: Fetch all articles, with optional filtering by category and author.
* Status: Passed ✅

####   Index Articles with Keyword Filter
* Test case: Successfully retrieves articles filtered by keyword
* Status: Passed ✅

####   Index Articles with Category Filter
* Test case: Successfully retrieves articles filtered by category
* Status: Passed ✅


####   User Personalized Feed (/api/user-personalized-feed/)
* Test case: Successfully retrieves personalized news feeds based on user preferences
* Status: Passed ✅

### User Preferences
####   Store User Preferences (/api/preferences)
* Test case: Successfully saves user preferences for personalized news feeds
* Status: Passed ✅

####   Get User Preferences (/api/preferences)
* Test case: Successfully retrieves user preferences
* Status: Passed ✅


Fetch Articles Command
* Test case: Successfully fetches articles from various news sources and stores them in the database
* Status: Passed ✅

## Running Test Cases
To run all test cases at once, use the following command:
```bash
  php artisan test
```

To run individual test cases, use the --filter option with the specific test case name. Here are a few examples:
```bash
  php artisan test --filter test_successful_registration
  php artisan test --filter test_successful_login
```

### Screenshots of Test Cases Run
<img width="1350" alt="Screenshot 2024-10-29 at 10 20 10 AM" src="https://github.com/user-attachments/assets/4643caef-960e-4284-a87f-86c8c1c8de51">
<img width="1321" alt="Screenshot 2024-10-29 at 10 21 29 AM" src="https://github.com/user-attachments/assets/3ff0e26e-bfd0-49f3-aa7c-f2d9f9e3a3f8">

## License
This project is licensed under the MIT License.




