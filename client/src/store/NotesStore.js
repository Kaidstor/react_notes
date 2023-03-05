import {makeAutoObservable} from "mobx";

export default class NotesStore {
    constructor() {
        this._tags = []
        this._filter = []
        this._notes = []
        this._selected_tags = new Map()
        makeAutoObservable(this)
    }
    setFilter(filter) {
        this._filter = filter
        let tags = new Map()

        for (let tag in filter){
            const {name, id} = filter[tag]
            if(tags.has(name))
                tags.set(name, {id, count: tags.get(name).count + 1})
            else
                tags.set(name, {id, count: 1})
        }
        this._tags = [...tags]
    }
    setNotes(notes) {
        this._notes = notes
    }
    setSelectedTag(tag){
        if(this._selected_tags.has(tag))
            this._selected_tags.delete(tag)
        else
            this._selected_tags.set(tag, true)

        const tagsId= [...this._selected_tags].reduce((acc, el) => [...acc, el[0]], [])
        localStorage.setItem('tags', JSON.stringify(tagsId))
    }
    get tags() {
        return this._tags
    }
    get filter() {
        return this._filter
    }
    get notes() {
        return this._notes
    }
    get selectedTags() {
        return this._selected_tags
    }
}