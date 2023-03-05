const Router = require('express')
const router = new Router()
const filesController = require('../controllers/filesController')

const multer = require('multer')
const uuid = require("uuid");
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'static/')
    },
    filename: function (req, file, cb) {
        const type = file.originalname.split('.').at(-1)
        cb(null, uuid.v4() + `.${type}`)
    }
})
const upload = multer({ storage: storage })


router.post('/', upload.array('file_data'),  filesController.create)
router.get('/', filesController.getAll)
router.get('/:id', filesController.getOne)

module.exports = router