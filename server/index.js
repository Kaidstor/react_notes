require('dotenv').config()
const express = require('express')
const sequelize = require('./db')
const models = require('./models/models')
const errorHandler = require('./middleware/errorHandlingMiddleware')
const path = require('path')
const router = require('./routes/index')
const cors = require('cors')
const PORT = process.env.PORT || 3001

const app = express()
app.use(cors())
app.use(express.json())
app.use('/api', router)
app.use(express.static(path.resolve(__dirname, 'static')))

// Обработка ошибок middleware
app.use(errorHandler)

const start = async () => {
    try{
        await sequelize.authenticate()
        await sequelize.sync()
        app.listen(PORT, () => {
            console.log(`Example app listening on port ${PORT}`)
        })
    }
    catch(e){
        console.log(e)
    }
}

start()