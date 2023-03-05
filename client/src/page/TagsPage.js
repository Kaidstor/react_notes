import React, {useContext, useEffect} from 'react';
import {Button, Container, Row} from "react-bootstrap";
import {observer} from "mobx-react-lite";
import {Context} from "../index";
import {fetchTagsInfo} from "../http/filterAPI";
import {useNavigate} from "react-router-dom";
import {NOTES_ROUTE} from "../utils/consts";

const TagsPage = observer(() => {
    const { notes } = useContext(Context)
    const navigate = useNavigate()

    useEffect(() => {
        fetchTagsInfo().then(data => notes.setFilter(data))
    }, [])

    return (
        <Container>
            <Row>
                <div className="mt-5 d-flex justify-content-center">
                    <h2>Найдём красоту :)</h2>
                </div>

                <div>

                    {notes.tags.map(tag => {
                        const name = tag[0]
                        const {id, count} = tag[1]
                        const active = notes.selectedTags.has(id) ? {color: 'red'} : {  }
                        return <div
                            onClick={() => {
                                notes.setSelectedTag(id)
                            }}
                            key={id ? id : 0}
                            style={active}
                            className="m-2"
                        >
                            {name ? name : 'Без тэга'} ({count})
                        </div>
                    })}
                </div>

                <Button
                    className="mt-2"
                    onClick={() => navigate('/' + NOTES_ROUTE)}
                >
                    К заметкам
                </Button>
            </Row>
        </Container>
    );
});

export default TagsPage;