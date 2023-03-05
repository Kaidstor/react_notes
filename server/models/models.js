const sequelize = require('../db')
const { DataTypes } = require('sequelize')

const User = sequelize.define('user', {
    id: {type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true},
    email: {type: DataTypes.STRING, unique: true},
    password: {type: DataTypes.STRING},
    permissions: {type: DataTypes.JSON, default: null}
})

const Note   = sequelize.define('note', {
    id: {type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true},
    description: {type: DataTypes.STRING, defaultValue: null}
})

const File = sequelize.define('file', {
    id: {type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true},
    filepath: {type: DataTypes.STRING}
})

const Tag = sequelize.define('tag', {
    id: {type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true},
    name: {type: DataTypes.STRING}
})

const FilterNote = sequelize.define('filterNote', {
    id: {type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true},
})

User.hasMany(Note)
Note.belongsTo(User)

Note.hasMany(File)
File.belongsTo(Note)

User.hasMany(Tag)
Tag.belongsTo(User)

Tag.belongsToMany(Note, {through: FilterNote})
Note.belongsToMany(Tag, {through: FilterNote})

module.exports = {
    User, Note, Tag, File, FilterNote
}
