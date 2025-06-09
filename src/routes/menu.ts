import { Router } from 'express';
import MenuController from '../controllers/menuController';

const router = Router();
const menuController = new MenuController();

router.get('/menu', menuController.getMenu.bind(menuController));
router.put('/menu', menuController.updateMenu.bind(menuController));

export default function setupMenuRoutes(app) {
    app.use('/api', router);
}