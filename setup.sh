#!/bin/bash

# Function to print section headers
print_section() {
    echo "=========================================="
    echo "  $1"
    echo "=========================================="
}

# Start Docker containers
print_section "Starting Docker containers"
docker compose up -d --build

# Wait for services to be ready
print_section "Waiting for services to be ready"
echo "Allowing time for services to initialize..."
sleep 10

# Install PHP dependencies
print_section "Installing PHP dependencies"
docker compose exec app composer install

if [ ! -f .env ]; then
    print_section "Creating .env file from .env.example"
    cp .env.example .env
    echo "Created .env file"
else
    print_section ".env file already exists"
fi

# Generate application key
print_section "Generating application key"
docker compose exec app php artisan key:generate

# Run database migrations
print_section "Running database migrations"
docker compose exec app php artisan migrate

# Install Node.js dependencies
print_section "Installing Node.js dependencies"
docker compose exec app npm install

print_section "Setup complete! Your Laravel application is running at http://localhost:8081"
echo ""
echo "Services running automatically:"
echo "  • PHP-FPM for serving your application"
echo "  • Laravel Scheduler (running in scheduler container)"
echo "  • npm run dev (running in npm-watcher container)"
echo ""
echo "To check running containers:"
echo "  docker compose ps"
echo ""
echo "To view logs for npm watcher:"
echo "  docker compose logs -f npm-watcher"
echo ""
echo "To view logs for scheduler:"
echo "  docker compose logs -f scheduler"
echo ""
echo "To stop the containers:"
echo "  docker compose down"
