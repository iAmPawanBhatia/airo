# Airo - Insurance Quotation System

- To install unzip and simply follow the steps below
- I have used the docker but you can run project as you want
- I have zipped everything except vendor folder. 

## Requirements

- Docker and Docker Compose
- Git
- Internet connection
    - Frontend has bootstrap css and js cnd https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css

## Installation & Setup

1. **Build and Run with Docker:**
   ```bash
   docker-compose up -d
   ```

2. **Install packages:**
  ```bash
   docker-compose exec app composer install
  ```

3. **Run Database Migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

4. **Access the Application:**
   - Web App: http://localhost:8080
   - API Base: http://localhost:8080/api

## Usage

### Web Interface
1. Register a new account or log in.
2. Fill in the quotation form with ages (comma-separated), currency, and dates.
3. Submit to get a quotation with total price and ID.

### API Usage

#### Get JWT Token
```bash
curl -X POST http://localhost:8080/api/auth/token \
  -H "Accept: application/json"
```

#### Request Quotation
```bash
curl -X POST http://localhost:8080/api/quotation \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "age": "28,35",
    "currency_id": "EUR",
    "start_date": "2026-03-27",
    "end_date": "2026-03-30"
  }'
```

#### Validation Rules
- Ages: 18-70, comma-separated
- Currency: EUR, GBP, USD
- Dates: Y-m-d format, start/end dates ≥ today, end ≥ start

## Developer

Pawan Bhatia - iampawanbhatia@gmail.com