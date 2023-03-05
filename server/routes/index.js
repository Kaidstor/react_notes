const Router = require('express')
const router = new Router()
const userRouter = require('./userRouter')
const noteRouter = require('./noteRouter')
const tagRouter = require('./tagRouter')
const filesRouter = require('./filesRouter')
const filterRouter = require('./filterRouter')

router.use('/user', userRouter)
router.use('/file', filesRouter)
router.use('/filter', filterRouter)
router.use('/note', noteRouter)
router.use('/tag', tagRouter)

module.exports = router