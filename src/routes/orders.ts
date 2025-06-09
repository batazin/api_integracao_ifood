import { Router } from 'express';
import OrderController from '../controllers/orderController';

const router = Router();
const orderController = new OrderController();

router.post('/orders', orderController.createOrder.bind(orderController));
router.get('/orders/:id', orderController.getOrder.bind(orderController));

export default function setupOrderRoutes(app) {
    app.use('/api', router);
}