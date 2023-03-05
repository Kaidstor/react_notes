import {$authHost} from './index'

export const createTag = async (note) => {
    const {data} = await $authHost.post('api/tag', {note})
    return data
}
export const fetchTags = async (tags=null) => {
    const {data} = await $authHost.get('api/tag')
    return data
}
export const fetchTagsInfo = async (tags=null) => {
    const {data} = await $authHost.get('api/filter/tags')
    return data
}