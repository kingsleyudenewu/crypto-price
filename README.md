# Crypto Price Checker
A solution for fetching realtime crypto prices with Laravel, livewire and laravel Echo


<p>
  <blockquote style="color:red">
    **Please follow the steps below to setup the application on your system** 
  </blockquote>
</p>  

## Required Versions
-PHP 8.2

## Installation Steps

- Clone project
- Run ```composer install``` for the main project
- Run ```add your pusher credentials on .env to connect Laravel Echo``` for the main project
- Run ```npm install``` for the main project
- Run ```npm run dev``` for the main project
- Rename .env.example to .env
- Create you database and set dbname, username and password on the new .env file
- Generate your laravel key : ```php artisan key:generate```
- Run ```php artisan schedule:work``` to set up the queue to run at every interval.
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
