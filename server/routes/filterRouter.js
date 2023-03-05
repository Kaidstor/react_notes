const Router = require('express')
const filterController = require('../controllers/filterController')
const router = new Router()

router.post('/', filterController.create)
router.get('/tags', filterController.getAllTags)
router.get('/notes', filterController.getAllNotes)

module.exports = router