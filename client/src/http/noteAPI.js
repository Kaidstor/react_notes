import {$authHost} from './index'

export const createNote = async (note) => {
    const {data} = await $authHost.post('api/note', note)
    return data
}
export const createFiles = async (formData) => {
    const {data} = await $authHost.post('api/file', formData)
    return data
}
export const fetchNotes = async (tags=null) => {
    console.log(tags)
    const {data} = await $authHost.get('api/note', { params: {tags: tags !== 'undefined' ? tags : null}})
    return data
}
export const fetchOneNote = async (id) => {
    const {data} = await $authHost.get('api/note/' + id)
    return data
}