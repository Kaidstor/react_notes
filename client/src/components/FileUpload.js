import React, {useContext, useEffect, useState} from 'react';
import {observer} from "mobx-react-lite";
import {Context} from "../index";
import {check} from "../http/userAPI";
import {Spinner} from "react-bootstrap";
import {createFiles, createNote} from "../http/noteAPI";

const FileUpload = observer(({children}) => {

    const {user} = useContext(Context)
    const [drag, setDrag] = useState(false)
    const [loading, setLoading] = useState(true)
    useEffect(() => {
        check().then(data => {
            user.setUser(data)
            user.setIsAuth(true)
        })
        .finally(() => setLoading(false))
    }, []);

    if (loading) {
        return <Spinner animation={"grow"}/>
    }


    function dragStartHandler(e) {
        e.preventDefault()
        setDrag(true)
    }

    function dragLeaveHandler(e) {
        e.preventDefault()
        setDrag(false)
    }

    async function onDropHandler(e) {
        try {
            e.preventDefault()
            const files = [...e.dataTransfer.files]
            // Закинутые файлы
            // console.log(files)
            let formData = new FormData()
            for (let i = 0; i < files.length; i++) {
                formData.append("file_data", files[i]);
            }
            const note = await createNote({userId: user.user.id, description: null})
            formData.append('noteId', note.id)
            // Созданная заметка
            // console.log(note)

            if (files.length) {
                // Созданные файлы, если переданы
                const file = await createFiles(formData)
                console.log(file)
            }
        } catch (e) {
            console.log(e.message)
        }
        setDrag(false)
    }

    return (drag
        ?
        <div
            className="drop-area"
            onDragStart={e => dragStartHandler(e)}
            onDragLeave={e => dragLeaveHandler(e)}
            onDragOver={e => dragStartHandler(e)}
            onDrop={e => onDropHandler(e)}
        >
            Отпустите файлы чтобы загрузить их
        </div>
        :
        <div
            className="full-hw"
            onDragStart={e => dragStartHandler(e)}
            onDragLeave={e => dragLeaveHandler(e)}
            onDragOver={e => dragStartHandler(e)}
        >
            {children}
        </div>)
})

export default FileUpload;