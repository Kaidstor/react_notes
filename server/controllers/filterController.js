const {FilterNote, Tag} = require("../models/models");
const sequelize = require('../db')
const ApiError = require('../error/ApiError')
class filterController {
    async create(req, res, next) {
        try{
            const {noteId} = req.body
            if (!noteId) next(ApiError.badRequest('Нет заметки, за которой закреплять фильтр'))
            const filter = await FilterNote.create({noteId, tagId: null})
            return res.json(filter)
        }
        catch(e) {
            next(ApiError.badRequest('Что-то пошло не так'))
        }
    }
    // Можно получить кол-во полученных строк из данного запроса
    async getAllTags(req, res) {
        const filter = await sequelize.query('SELECT "noteId", "tagId" as id, "name" FROM "filterNotes" LEFT OUTER JOIN  tags on "filterNotes"."tagId" = tags.id')
        return res.json(filter[0])
    }
    async getAllNotes(req, res) {
        const filter = await sequelize.query('SELECT "noteId", "tagId", "name" FROM "filterNotes" LEFT OUTER JOIN  tags on "filterNotes"."tagId" = tags.id')
        return res.json(filter[0])
    }
}

module.exports = new filterController()