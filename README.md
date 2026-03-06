# Shop Order Management API

This project is a RESTful API built with Laravel 10 for managing shop orders, products, and inventory. 
The API allows shops to create orders, manage stock, cancel orders, and generate reports such as top-selling products.
The system ensures data consistency using database transactions and row-level locking to prevent concurrency issues during stock updates.

## Setup Instructions

1. Clone the repository
git clone https://github.com/windy-w25/shop-order-api.git

    cd shop-order-api

2. Install dependencies
composer install

3. Copy environment file
cp .env.example .env

4. Generate application key
php artisan key:generate

## Environment Configuration

Update the ,env file with your database configuration.
Example:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shop_order_db
DB_USERNAME=root
DB_PASSWORD=

## Running the Application

php artisan migrate

Start the server:
php artisan serve

API will be available at:
http://127.0.0.1:8000

## API Endpoints

Create Order
POST /api/orders

Get Orders
GET /api/orders

Get Single Order
GET /api/orders/{id}

Cancel Order
PATCH /api/orders/{id}/cancel

Top Products Report
GET /api/reports/top-products

## Running Tests

Run all tests:
php artisan test

Run specific test:
php artisan test --filter=CreateOrderTest

## Assumptions

- Products must exist before placing an order.
- Each order belongs to one shop.
- Product price at the time of purchase is stored in the order_items table.
- Orders with status "cancelled" cannot be cancelled again.
- Stock must be available before creating an order.


## Debug & Concurrency Problem

1. The issue occurs because multiple transactions read the same stock value simultaneously before it is updated. Since the stock check and update are not performed atomically with a database lock, concurrent requests can reduce stock below zero.

2. To prevent negative stock during concurrent order placement, the application uses database transactions combined with lockForUpdate() to ensure row-level locking. This guarantees that stock validation and updates occur atomically, preventing race conditions.

## Architecture & Design Question

### Asynchronous Payments and Refunds Design

To support asynchronous payments and refunds, I would use Laravel queues, webhook handling, and proper database safeguards to ensure reliable processing.

### Queue Structure

Payment and refund processing would run through Laravel queue jobs instead of executing directly in the request. When a webhook is received, the system would dispatch a job such as ProcessPaymentWebhook or ProcessRefundWebhook. This keeps the webhook response fast and allows the heavy processing to happen in the background. Laravel queue workers would handle these jobs using Redis or database queues.

### Idempotency

To ensure the same webhook is not processed multiple times, each payment event from the payment provider should include a unique event ID. This ID would be stored in the database in a payment_events or webhook_events table. Before processing a webhook, the system would check whether that event ID already exists. If it does, the request would simply be ignored.

### Preventing Duplicate Processing

Duplicate processing can also be prevented at the database level. Unique constraints can be applied to fields such as payment_provider_event_id or transaction_id. This ensures that even if a job runs twice, the database will reject duplicates.

### Webhook Validation

For security, webhook requests should be verified before processing. Most payment providers send a signature header. The application should validate this signature using a shared secret to confirm that the request really came from the payment provider and was not modified.

### Retry Strategy

If a queue job fails due to temporary issues such as network errors, Laravel’s built-in retry system can automatically retry the job several times with delays. This helps handle temporary failures without losing the event.

### Failure Handling

If a job continues to fail after multiple retries, it will be moved to Laravel’s failed jobs table. These failed jobs can be monitored and manually retried after the issue is resolved.

### Database Design

The database should include tables for payments, refunds, and webhook events. Storing webhook event IDs and transaction IDs ensures traceability and helps enforce idempotency. Proper indexing and unique constraints help maintain consistency and prevent duplicate records.