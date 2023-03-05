import React, {useEffect, useState} from 'react';
import {Link, useParams} from "react-router-dom";
import {fetchOneNote} from "../http/noteAPI";
import {Container} from "react-bootstrap";

function Loa() {
    return null;
}

const NotePage = () => {
    const [note, setNote] = useState({})
    const {id} = useParams()
    useEffect(() => {
        fetchOneNote(id).then(data => setNote(data))
    }, [])
    return (
        <Container>

            {note?
                <>
                    <h1>Заметка {note.name ? note.name : 'без названия'}</h1>
                    <p>Описание {note.description ? note.description : 'без описания'}</p>
                    {note.files ?
                        <>
                            <p>Файлы</p>
                            {
                                note.files.map(el => <Link className="d-flex mt-2" key={el.id} to={process.env.REACT_APP_API_URL + el.filepath}>Файл {el.id}</Link>)
                            }
                        </>
                        :
                        <p>Нет файлов</p>
                    }
                </>

                :
                <div>Loading..</div>
            }

        </Container>
    );
};

export default NotePage;