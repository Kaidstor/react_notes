const {Note, File, FilterNote} = require("../models/models");
const ApiError = require('../error/ApiError')
const {Op} = require("sequelize")

class noteController {
    async create(req, res, next) {
        try {
            const {userId, description} = req.body
            if (!userId) next(ApiError.badRequest('Вы не авторизованы'))
            const note = await Note.create({description, userId})
            const filterNote = await FilterNote.create({noteId: note.id, tagId: null})
            console.log(filterNote)
            return res.json(note)
        } catch (e) {
            next(ApiError.badRequest('Что-то пошло не так'))
        }
    }

    async getAll(req, res) {
        let {tags} = req.query
        tags = tags ? JSON.parse(tags) : null;

        const tagId = tags?.length
            ?
                tags.includes(null)
                    ? {[Op.or]: {[Op.in]: tags, [Op.eq]: null}}
                    : {[Op.in]: tags}
            :
                {[Op.eq]: null}

        const notesIds = await FilterNote.findAll({
            attributes: ['noteId'],
            where: {tagId},
            raw: true,
        })
        const ids = notesIds.map(item => item.noteId)
        const notes = await Note.findAll({where: {id: ids}})
        return res.json(notes)
    }

    async getOne(req, res) {
        const {id} = req.params
        const note = await Note.findOne(
            {
                where: {id},
                include: [{model: File, as: 'files'}]
            },
        )
        // console.log(notes)
        return res.json(note)

    }
}

module.exports = new noteController()