# Time Tracking Application

Welcome to the **Time Tracking Application**, a Symfony-based project designed to manage employee work hours, calculate summaries, and handle overtime payments. Built with **Symfony 7.2.4**, **PHP 8.4.3**, and **MariaDB 11.3**, this application provides a robust foundation for tracking and reporting work time.

## Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)

## Overview
This project implements a clean architecture using Symfony, featuring a **modular service layer**, **strategy pattern** for summary calculations, and **Doctrine ORM** for persistence. It allows employees to **register work hours**, calculates **standard and overtime pay**, and provides **monthly/daily summaries**.

## Features
- **Employee management** with unique UUID identifiers.
- **Work time registration** with start/end times and daily limits (max 12 hours).
- **Summary generation** for daily or monthly periods with rounded hours (30-minute increments).
- **Configurable rates**:
    - Standard: **20 PLN/hour**
    - Overtime: **40 PLN/hour**
    - Monthly norm: **40 hours**
- **RESTful API endpoints** for creating employees, registering work time, and retrieving summaries.

## Prerequisites
Ensure you have the following installed:
- **PHP 8.4.3** or higher ([Download PHP](https://www.php.net/downloads.php))
- **Composer** ([Install Composer](https://getcomposer.org/download/))
- **Symfony CLI** ([Install Symfony CLI](https://symfony.com/download))
- **MariaDB 11.3** or higher ([Download MariaDB](https://mariadb.org/download/))
- **Docker** (optional, for running MariaDB in a container) ([Install Docker](https://www.docker.com/get-started))
- **Git** ([Install Git](https://git-scm.com/downloads))

## Installation
### 1. Clone the Repository
```bash
git clone git@github.com:mikoajp/time_tracking.git
cd time_tracking
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Database
#### Option 1: Using a Local MariaDB Server
Copy `.env` to `.env.local` and update the database settings:
```env
MONTHLY_NORM=40
BASE_RATE=20
OVERTIME_MULTIPLIER=2.0
```

#### Option 2: Using Docker for MariaDB
Start a MariaDB container:
```bash
docker-compose up -d
```
Update `.env.local`:
```env
DATABASE_URL=mysql://app_user:app_password@127.0.0.1:3333/time_tracking_system?serverVersion=mariadb-11.3.2&charset=utf8mb4
```

### 4. Create the Database
```bash
php bin/console doctrine:database:create
```

### 5. Run Migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 6. Start the Server
```bash
symfony server:start
```

## Usage
- Access the application at **[http://localhost:8000](http://localhost:8000)**.
- Use the **API endpoints** (see below) with tools like **Postman** or **cURL**.

### Example: Register Work Time
```bash
$body = @{
    firstName = "Janek"
    lastName = "Kowalski"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/api/employees" -Method Post -Headers @{"Content-Type"="application/json"} -Body $body```
```
**Expected response:**
```json
{"message": "123e4567-e89b-12d3-a456-426614174000!"}
```


### Example: Register Work Time
```bash
curl -X POST http://localhost:8000/work-time \
-H "Content-Type: application/json" \
-d '{"employee_id": "123e4567-e89b-12d3-a456-426614174000", "start": "2025-03-01 08:00:00", "end": "2025-03-01 14:00:00"}'
```
**Expected response:**
```json
{"message": "Czas pracy został dodany!"}
```

### Example: Get Monthly Summary
```bash
curl http://localhost:8000/summary/123e4567-e89b-12d3-a456-426614174000/2025-03
```
**Expected response:**
```json
{
  "response": {
    "ilość normalnych godzin z danego miesiąca": 6.0,
    "stawka": "20 PLN",
    "ilość nadgodzin z danego miesiąca": 0.0,
    "stawka nadgodzinowa": "40 PLN",
    "suma po przeliczeniu": "120.00 PLN"
  }
}
```

## API Endpoints
| Method | Endpoint | Description | Request Body |
|--------|---------|-------------|--------------|
| **POST** | `/employees` | Create a new employee | `{ "imie": "Jan", "nazwisko": "Kowalski" }` |
| **POST** | `/work-time` | Register work time for an employee | `{ "employee_id": "uuid", "start": "YYYY-MM-DD HH:MM:SS", "end": "YYYY-MM-DD HH:MM:SS" }` |
| **GET** | `/summary/{employeeId}/{date}` | Get work time summary (day/month) | N/A (path parameters: UUID and date) |

## Configuration
Application parameters are managed via `.env`:
- **MONTHLY_NORM**: Monthly hour norm (default: **40**).
- **BASE_RATE**: Standard hourly rate (default: **20 PLN**).
- **OVERTIME_MULTIPLIER**: Overtime rate multiplier (default: **2.0**).

For advanced configurations, update `services.yaml` as needed.

## Contributing
1. Fork the repository.
2. Create a feature branch:
   ```bash
   git checkout -b feature/new-feature
   ```
3. Commit changes:
   ```bash
   git commit -m "Add new feature"
   ```
4. Push to the branch:
   ```bash
   git push origin feature/new-feature
   ```
5. Open a Pull Request.

## License
This project is licensed under the **MIT License**. See the `LICENSE` file for details.

