# iFood Integration Project

This project provides a PHP integration with the iFood API, allowing for order management and menu retrieval.

## Project Structure

```
ifood-integration
├── src
│   ├── index.php              # Entry point of the application
│   ├── config
│   │   └── config.php         # Configuration settings (API keys, DB connection)
│   ├── services
│   │   ├── IfoodService.php    # Handles communication with the iFood API
│   │   └── AuthService.php     # Manages authentication with the iFood API
│   ├── controllers
│   │   ├── OrderController.php  # Handles order-related requests
│   │   └── MenuController.php   # Handles menu-related requests
│   └── models
│       ├── Order.php           # Represents an order in the application
│       └── Product.php         # Represents a product in the application
├── composer.json               # Composer configuration file
└── README.md                   # Project documentation
```

## Setup Instructions

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/ifood-integration.git
   ```

2. Navigate to the project directory:
   ```
   cd ifood-integration
   ```

3. Install dependencies using Composer:
   ```
   composer install
   ```

4. Configure your API keys and database connection in `src/config/config.php`.

5. Start the application by accessing `src/index.php` through your web server.

## Usage Examples

- To place an order, use the `OrderController` class.
- To retrieve menu items, use the `MenuController` class.

## Contributing

Feel free to submit issues or pull requests for improvements or bug fixes.