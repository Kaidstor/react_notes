const ApiError = require("../error/ApiError");
const {User} = require("../models/models");
const bcrypt = require('bcrypt')
const jwt = require('jsonwebtoken')

const generateJWT = (id, email) => {
    return jwt.sign(
        {id, email},
        process.env.SECRET_KEY,
        {expiresIn: '24h'}
    )
}

class UserController {
    async registration(req, res, next) {
        try {
            const {email, password} = req.body
            console.log(req.body)
            if (!email || !password){
                return next(ApiError.badRequest('Некорректный email или пароль'))
            }
            const haveUser = await User.findOne({where: {email}})
            if (haveUser){
                return next(ApiError.badRequest('Пользователь с таким email уже существует'))
            }

            const hashPassword = await bcrypt.hash(password, 5)
            const user = await User.create({email, password: hashPassword})
            const token = generateJWT(user.id, user.email)
            return res.json({token})
        }
        catch(e){
            next(ApiError.badRequest(e.message))
        }
    }
    async login(req, res, next) {
        const {email, password} = req.body
        const user = await User.findOne({where: {email}})
        if (!user)
            return next(ApiError.unauthorized('Пользователь не найден'))
        if(!password)
            return next(ApiError.unauthorized('Не указан пароль!'))

        let comparePassword = bcrypt.compareSync(password, user.password)
        if(!comparePassword)
            return next(ApiError.unauthorized('Введён не верный пароль'))
        const token = generateJWT(user.id, user.email)
        return res.json({token})
    }
    async getAll(req, res) {
        const users = await User.findAll()
        return(res.json(users))
    }
    async check(req, res, next) {
        const token = generateJWT(req.user.id, req.user.email)
        return res.json({token})
    }
}

module.exports = new UserController()