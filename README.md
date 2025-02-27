# Crypto Price Checker

A solution for fetching realtime crypto prices with Laravel, Livewire, and Laravel Echo. This application provides live updates of cryptocurrency prices without requiring page refreshes by fetching prices from major exchanges concurrently.

## How It Works

This application employs a multi-component architecture to deliver real-time cryptocurrency price updates:

1. **Scheduled Jobs**: A Laravel scheduled task runs at regular intervals to fetch the latest cryptocurrency prices from external APIs.

2. **Queue Processing**: The fetched data is processed through Laravel's queue system, ensuring the application remains responsive during data processing.

3. **Broadcasting**: When new price data is available, the system broadcasts events using Laravel Echo and Pusher.

4. **Real-time Frontend**: The Livewire components on the frontend listen for these broadcast events and automatically update the UI with the latest pricing data without requiring page refreshes.

5. **Data Flow**:
    - API calls fetch latest crypto prices →
    - Data is processed and stored in database →
    - Events are broadcast via Pusher →
    - Livewire components receive events →
    - UI updates with new prices in real-time

## Required Versions

- PHP 8.2
- Laravel 10.x
- Node.js 18+ (for npm commands)

## Installation Steps

1. **Clone and Setup Project**
   ```bash
   # Clone the repository
   git clone https://github.com/yourusername/crypto-price-checker.git
   cd crypto-price-checker
   
   # Install all dependencies in one command
   chmod +x setup.sh
   
   # Run the setup command and allow docker and every dependecies to be installed properly
    ./setup.sh
   
   # update the database credentials on your .env file
   DB_CONNECTION=xxxx
   DB_HOST=xx
   DB_PORT=1234
   DB_DATABASE=xxxx
   DB_USERNAME=xxxx
   DB_PASSWORD=xxxx
   
   Also add your push credentials to the .env file 
   
   # Build your node assets
   npm run dev
