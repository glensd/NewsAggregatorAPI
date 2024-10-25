# News Aggregator API

Welcome to the News Aggregator API! This is a RESTful API built with Laravel that allows users to manage articles, authenticate, and customize their news preferences.

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Usage](#usage)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features
- User Authentication using Laravel Sanctum (registration, login, logout, password reset)
- Article Management (fetch, search, and retrieve articles)
- User Preferences (to be implemented in subsequent steps)

## Requirements
- PHP 8.0 or higher
- Composer
- Laravel 8 or higher
- MySQL or any compatible database

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd news-aggregator

2. Install dependencies:
    composer install

3.Create a copy of the .env.example file and rename it to .env:
    cp .env.example .env

4. Generate the application key:
    php artisan key:generate

5.Set up your database connection in the .env file.
6.Run the migrations:
    php artisan migrate

7.Start the server:
    php artisan serve

**API Endpoints
User Authentication
    Register a new user**
    POST /api/register
    - Body: { "name": "user", "email": "user@example.com", "password": "password", "password_confirmation": "password" }
  **Login**    
    POST /api/login
    - Body: { "email": "user@example.com", "password": "password" }
    **Logout**
    POST /api/logout
    **Reset Password**
    POST /api/reset-password
    - Body: { "email": "user@example.com", "new_password": "newPassword123" }

**Article Management**
    **Fetch all articles**
    GET - /api/articles
    **Search articles**
    GET - /api/articles/search?keyword=someKeyword
    **Retrieve a single article**
    GET - /api/articles/{id}

**Usage**
After setting up the API and starting the server, you can use tools like Postman or cURL to interact with the API endpoints.
