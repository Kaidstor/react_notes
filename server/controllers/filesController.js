const {File} = require("../models/models");
const ApiError = require('../error/ApiError')
class filesController {
    async create(req, res, next) {
        try{
            const { noteId } = req.body
            const files = []
            for(let file of req.files){
                // Если надо будет оригинальное имя
                //originalname: 'vlcsnap-2021-03-03-20h20m11s729.png'
                const new_file = await File.create({noteId, filepath: file.filename})
                files.push(new_file)
            }
            return res.json(files)
        }
        catch(e){
            next(ApiError.badRequest('Что-то пошло не так'))
        }
    }
    async getAll(req, res) {
        const files = await Files.findAll()
        return res.json(files)
    }
    async getOne(req, res) {

    }
}

module.exports = new filesController()