# News Aggregator API

## Overview
The News Aggregator API is a backend service built with Laravel that allows users to access news articles from various sources, personalized according to their preferences. This API fetches articles from multiple news APIs, including NewsAPI, The Guardian, and The New York Times.

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Commands Scheduled](#commands-scheduled)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [License](#license)

## Features
- User authentication (registration, login, logout, password reset)
- Article management (fetch, store, update, and retrieve articles)
- User preferences management (set and retrieve preferred news sources, categories, and authors)
- Personalized news feed based on user preferences

## Requirements
- PHP 8.0 or higher
- Composer
- Laravel 8 or higher
- MySQL or any compatible database

## Technologies Used
- Laravel 10
- MySQL
- HTTP Client (for API requests)
- Caching (for optimized performance)
- Swagger/OpenAPI (for API documentation)

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

# API Endpoints
## Authentication

### User registration
- POST /api/register

### User login
- POST /api/login

### User logout
- POST /api/logout
    
### Reset Password
- POST /api/reset-password

## Article Management
### Retrieve articles with filters (title, content, source, date, category)
- GET /api/articles

###  Retrieve a specific article
-   GET /api/articles/{id}

### Retrieve personalized news feed based on user preferences
- GET /api/user-personalized-feed/{user_id}

## User Preferences
### Set user preferences (news sources, categories, authors)
-  POST /api/preferences

### Retrieve user preferences
- GET /api/preferences/{userId}

## Fetching Articles
To regularly fetch and store articles from news APIs, run the following command:
``` bash
php artisan articles:fetch
```

## API Documentation
Comprehensive API documentation is available using Swagger/OpenAPI. To access it, run the following command:
``` bash
php artisan l5-swagger:generate
```
Then navigate to http://localhost:8000/api/documentation.
You can view the API documentation by downloading and opening the `index.html` file locally:
- https://glensd.github.io/newsaggregator-api/

## Testing
Below is a summary of the API test cases and their status:
- Registration (/api/register)
  - Test case: Successful user registration
  - Status: Passed ✅


- Login (/api/login)
  - Test case: Successful login with valid credentials
  - Status: Passed ✅
  - Test case: Attempt to login with non-existent email
  - Status: Passed ✅
  - Test case: Attempt to login with incorrect password
  - Status: Passed ✅
  - Test case: Attempt to login without password
  - Status: Passed ✅


- Forgot Password (/api/forgot-password)
    - Test case: Email exists, reset link sent
    - Status: Passed ✅ 
    - Test case: Email does not exist, validation error
    - Status: Passed ✅
  

- Reset Password (/api/reset-password)

    - Test case: Valid token and new password provided
    - Status: Passed ✅
    - Test case: Invalid token, unable to reset password
    - Status: Passed ✅


-   Logout (/api/logout)
    - Test case: Successful logout with email provided
    - Status: Passed ✅ 
    - Test case: Attempt to logout with invalid email
    - Status: Passed ✅

## Running Test Cases
To run all test cases at once, use the following command:
```bash
 php artisan test
```

To run individual test cases, use the --filter option with the specific test case name. Here are a few examples of a running test case individually:
```bash
    php artisan test --filter test_successful_registration
```

```bash
    php artisan test --filter test_successful_login
```
## License
This project is licensed under the MIT License.




