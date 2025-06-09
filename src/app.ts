import express from 'express';
import { json } from 'body-parser';
import { setupWebhookRoutes } from './routes/webhook';
import { setupOrderRoutes } from './routes/orders';
import { setupMenuRoutes } from './routes/menu';
import { connectDatabase } from './config/database';

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(json());

// Database connection
connectDatabase();

// Routes
setupWebhookRoutes(app);
setupOrderRoutes(app);
setupMenuRoutes(app);

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});