import React, {useContext, useEffect} from 'react';
import {Container, Row} from "react-bootstrap";
import {fetchNotes} from "../http/noteAPI";
import {Context} from "../index";
import {observer} from "mobx-react-lite";
import {Link} from "react-router-dom";

const NotesPage = observer(() => {
    const {notes} = useContext(Context)
    useEffect(() => {
        fetchNotes(localStorage.getItem('tags')).then(data => notes.setNotes(data))
    }, [])

    return (
        <Container>
            <div className="my-4 d-flex justify-content-center">
                <h2>
                    {notes.selectedTags.size
                        ? 'Здесь будут указаны выбранные тэги'
                        : 'Нераспределённые заметки'
                    }
                </h2>
            </div>
            <Row>
                {
                    notes.notes.map(note =>
                        <Link to={'/note/' + note.id} key={note.id} style={{
                            background: '#0d6efd',
                            borderRadius: 10,
                            marginBottom: 20,
                            padding: 10,
                            color: 'white'
                        }}>
                            <p>{note.createdAt}</p>
                            <p>Описание: {note.description ? note.description : 'Нет описания'}</p>
                        </Link>
                    )
                }
            </Row>
        </Container>
    );
});

export default NotesPage;