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
<img width="150" alt="Screenshot 2024-10-29 at 3 57 54 PM" src="https://github.com/user-attachments/assets/94bfd102-cc83-4c96-8ce9-d651de03e571">
<img width="150" alt="Screenshot 2024-10-29 at 3 58 03 PM" src="https://github.com/user-attachments/assets/36fa8a68-d508-4e98-9c62-113e5d506925">
<img width="150" alt="Screenshot 2024-10-29 at 3 58 41 PM" src="https://github.com/user-attachments/assets/bba99ac3-ec33-4461-bf42-0b805b6ac73f">
<img width="150" alt="Screenshot 2024-10-29 at 3 59 56 PM" src="https://github.com/user-attachments/assets/f592ca50-fdd6-4c9e-b6f3-4e7b78a4752f">
<img width="150" alt="Screenshot 2024-10-29 at 4 00 20 PM" src="https://github.com/user-attachments/assets/3b69496d-c2cf-4493-b878-35d26c8e6c26">
<img width="150" alt="Screenshot 2024-10-29 at 4 00 35 PM" src="https://github.com/user-attachments/assets/c2063d2c-dcca-42f5-b32b-ca1774c5a208">
<img width="150" alt="Screenshot 2024-10-29 at 4 04 53 PM" src="https://github.com/user-attachments/assets/925651d7-e638-47b8-82ed-742d5e380f32">
<img width="150" alt="Screenshot 2024-10-29 at 4 05 10 PM" src="https://github.com/user-attachments/assets/c7f8f291-6e6a-420c-859e-c04b44f99b7c">
<img width="150" alt="Screenshot 2024-10-29 at 4 06 10 PM" src="https://github.com/user-attachments/assets/a95a2cc6-8ccb-4945-9294-d623d0417adc">
<img width="150" alt="Screenshot 2024-10-29 at 4 20 37 PM" src="https://github.com/user-attachments/assets/f9b5aafe-a917-41b0-8ae1-4a779160e084">
<img width="150" alt="Screenshot 2024-10-29 at 4 20 52 PM" src="https://github.com/user-attachments/assets/9023f283-377e-4cc1-a78a-95c3e0a4e305">
<img width="150" alt="Screenshot 2024-10-29 at 4 21 17 PM" src="https://github.com/user-attachments/assets/f16eeb2a-9b7e-4472-8758-60f75c932f09">
<img width="150" alt="Screenshot 2024-10-29 at 4 23 06 PM" src="https://github.com/user-attachments/assets/4914957b-4053-42dd-9473-d47fc8b54519">
<img width="150" alt="Screenshot 2024-10-29 at 4 12 40 PM" src="https://github.com/user-attachments/assets/c449d19f-5993-4c81-a695-63571e8333ab">
<img width="150" alt="Screenshot 2024-10-29 at 4 12 50 PM" src="https://github.com/user-attachments/assets/03dfc3d0-b84c-4bba-b330-3710646883e8">
<img width="150" alt="Screenshot 2024-10-29 at 4 10 15 PM" src="https://github.com/user-attachments/assets/ea4b49a6-dfa1-4b70-97da-88354a34b937">
<img width="150" alt="Screenshot 2024-10-29 at 4 10 31 PM" src="https://github.com/user-attachments/assets/dc68af59-c214-4121-9f79-7c6a5555894f">
<img width="150" alt="Screenshot 2024-10-29 at 4 10 54 PM" src="https://github.com/user-attachments/assets/ef0e38d3-f5fa-4a55-b424-10a478aa8a4c">
<img width="150" alt="Screenshot 2024-10-29 at 4 11 08 PM" src="https://github.com/user-attachments/assets/acfa4ee9-835a-4dc3-b16b-0073ebb27e61">
<img width="150" alt="Screenshot 2024-10-29 at 4 14 49 PM" src="https://github.com/user-attachments/assets/15bc5154-d103-4dd1-a5dd-33a950cbbf2a">
<img width="150" alt="Screenshot 2024-10-29 at 4 15 05 PM" src="https://github.com/user-attachments/assets/da8db125-dadc-4e9c-94ba-84f4a0075bc1">
<img width="150" alt="Screenshot 2024-10-29 at 4 16 47 PM" src="https://github.com/user-attachments/assets/c80afc9d-b087-44b4-b5b2-dc93379ba430">
<img width="150" alt="Screenshot 2024-10-29 at 4 17 06 PM" src="https://github.com/user-attachments/assets/db39db4f-9c87-4f8f-af80-cb2247e31a75">
<img width="150" alt="Screenshot 2024-10-29 at 4 18 38 PM" src="https://github.com/user-attachments/assets/c5d6e9a0-101b-4843-983c-95047a253cf9">
<img width="150" alt="Screenshot 2024-10-29 at 4 18 57 PM" src="https://github.com/user-attachments/assets/1dcc4b6c-93c4-488a-b2b1-1998b7319ae6">
<img width="150" alt="Screenshot 2024-10-29 at 4 25 51 PM" src="https://github.com/user-attachments/assets/58f48579-527b-4c52-939d-9d8ebdfcfa86">
<img width="150" alt="Screenshot 2024-10-29 at 4 26 00 PM" src="https://github.com/user-attachments/assets/00646787-dcf7-4490-ae9c-8ff317aa3e1a">
<img width="150" alt="Screenshot 2024-10-29 at 4 26 18 PM" src="https://github.com/user-attachments/assets/5dac8937-250c-48a1-8fe4-b25e535631a3">
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




