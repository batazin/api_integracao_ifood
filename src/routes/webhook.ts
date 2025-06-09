import { Router } from 'express';
import WebhookController from '../controllers/webhookController';

const router = Router();
const webhookController = new WebhookController();

export function setupWebhookRoutes(app) {
    app.use('/webhook', router);

    router.post('/', webhookController.handleWebhook.bind(webhookController));
}