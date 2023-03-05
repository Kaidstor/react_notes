const Router = require('express')
const userController = require('../controllers/userController')
const authMiddleware = require('../middleware/authMiddleware')
const router = new Router()

router.post('/registration', userController.registration)
router.post('/login', userController.login)
router.post('/auth', authMiddleware ,userController.check)
router.get('/', userController.getAll)

module.exports = router