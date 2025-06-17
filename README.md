# iFood Integration Project

This project provides a PHP integration with the iFood API, allowing for order management and menu retrieval.

## Endpoint analistic

​/orders​/{id}​/virtual-bag
/orders/{id}/tracking
/orders/{id}/requestDriver
/orders/{id}/cancelRequestDriver
/disputes/{disputeId}/accept
/disputes/{disputeId}/reject
/orders/{id}/validatePickupCode
/orders/{id}/verifyDeliveryCode
​/orders​/{id}​/assignDriver
/orders/{id}/verifyDeliveryCode
/merchants/{merchantId}/deliveryAvailabilities
​/orders​/{orderId}​/deliveryAvailabilities
/merchants/{merchantId}/orders
/orders/{orderId}/requestDriver
/orders/{orderId}/acceptDeliveryAddressChange
/orders/{orderId}/deliveryAddressChangeRequest
​/orders​/{orderId}​/denyDeliveryAddressChange
/orders/{orderId}/userConfirmAddress
​/orders​/{orderId}​/cancellationReasons
/orders/{orderId}/cancel
/orders/{orderId}/cancelRequestDriver
/orders/{id}/tracking
/merchants/{merchantId}/catalogs/{groupId}/sellableItems
/merchants/{merchantId}/catalogs/{catalogId}/categories - post
/merchants/{merchantId}/products - get



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