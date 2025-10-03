# ProjectTasks

## 🚀 Features

- **Task Management**: Create, update, and track tasks across projects
- **Project Organization**: Organize tasks within projects
- **User Authentication**: Secure login and registration with Laravel Fortify
- **Email Notifications**: Daily digest emails for task updates
- **Role-based Permissions**: Secure access control with policies
- **API Ready**: RESTful API endpoints available

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: React, TypeScript, Inertia.js
- **Database**: MySQL
- **Caching**: Redis
- **Queue System**: Laravel Queues
- **Build Tools**: Vite, NPM
- **Testing**: Pest PHP
- **Containerization**: Docker & Docker Compose

## 📋 Prerequisites

- Docker and Docker Compose
- Make (for running commands)

## 🐳 Docker Services

- **app**: PHP-FPM with Laravel application
- **nginx**: Web server
- **db**: MySQL database
- **redis**: Redis cache and sessions
- **mailhog**: Email testing tool

## 🚀 Quick Start

1. **Clone the repository**
2. **Application Setup**

```bash
  make install
 ```

3. **Environment Configuration**: Double-check `.env` file for database and mail settings.

```bash
   DB_HOST=db
   MAIL_HOST=mailhog
   REDIS_HOST=redis
```

4. **Database Migration & Seeding**

```bash
  make migrate
  make seed
```

5. Run the application

```bash
  make up
```

6. Access the application at [http://localhost:8081](http://localhost:8081)

## 🧪 Running Tests

```bash
  make test
```

## ⚡ Available Commands

```bash
  make install        # Install dependencies and set up the application
  make migrate        # Run database migrations
  make migrate-fresh  # Drop all tables and re-run all migrations
  make seed           # Seed the database with initial data
  make test           # Run the test suite
  make up             # Start the Docker containers
  make down           # Stop the Docker containers
  make restart        # Restart the Docker containers
  make build          # Rebuild the Docker containers
```
