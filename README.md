# api_integracao_ifood
This project integrates the iFood API with a Point of Sale (PDV) system using webhooks. It allows for real-time order processing and menu management.

## Project Structure
```
api_integracao_ifood
├── src
│   ├── app.ts
│   ├── controllers
│   │   ├── webhookController.ts
│   │   ├── orderController.ts
│   │   └── menuController.ts
│   ├── services
│   │   ├── ifoodService.ts
│   │   └── pdvService.ts
│   ├── middlewares
│   │   ├── auth.ts
│   │   └── validation.ts
│   ├── routes
│   │   ├── webhook.ts
│   │   ├── orders.ts
│   │   └── menu.ts
│   ├── models
│   │   ├── Order.ts
│   │   ├── Product.ts
│   │   └── Webhook.ts
│   ├── config
│   │   └── database.ts
│   └── types
│       └── index.ts
├── tests
│   ├── controllers
│   └── services
├── package.json
├── tsconfig.json
├── .env.example
└── README.md
```

## Setup Instructions
1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd api_integracao_ifood
   ```
3. Install dependencies:
   ```
   npm install
   ```
4. Create a `.env` file based on the `.env.example` file and configure your environment variables.
5. Start the application:
   ```
   npm start
   ```

## Usage
- The application listens for webhook events from iFood and processes them accordingly.
- You can manage orders and menus through the provided endpoints.

## Testing
- Tests are located in the `tests` directory. Run the tests using:
  ```
  npm test
  ```

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License.