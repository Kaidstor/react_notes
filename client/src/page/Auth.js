import React, {useContext, useState} from 'react';
import {Button, Card, Container, Form, Row} from "react-bootstrap";
import {LOGIN_ROUTE, REGISTRATION_ROUTE, TAGS_ROUTE} from "../utils/consts";
import {NavLink, useLocation, useNavigate} from "react-router-dom";
import {login, registration} from "../http/userAPI";
import {observer} from "mobx-react-lite";
import {Context} from "../index";

const Auth = observer(() => {
    const navigate = useNavigate()
    const location = useLocation()

    const fromPage = location?.state?.from?.pathname || '/'

    const { user }  = useContext(Context)
    const isLogin = location.pathname === '/login'

    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const click = async () => {
        try{
            let data;
            if (isLogin)
                data = await login(email, password)
            else
                data = await registration(email, password)

            user.setUser(data)
            user.setIsAuth(true)

            navigate(`/${TAGS_ROUTE}`)
        }
        catch (e){
            alert(e.response.data.message)
        }
    }

    return (
        <Container
            className="d-flex justify-content-center align-items-center"
            style={{height: window.innerHeight - 54}}
        >
            <Card style={{width: 500}} className="p-4">
                <h2 className="m-auto">{isLogin ? 'Авторизация' : 'Регистрация'}</h2>
                <Form className="d-flex flex-column">
                    <Form.Control
                        className="mt-4"
                        placeholder="Введите ваш email..."
                        value={email}
                        onChange={e=>setEmail(e.target.value)}
                    />
                    <Form.Control
                        className="mt-2"
                        placeholder="Введите ваш пароль..."
                        value={password}
                        onChange={e=>setPassword(e.target.value)}
                        type="password"
                        autoComplete="on"
                    />
                    <Row className="d-flex justify-content-between mt-3 ps-3 pe-3">
                         <div className="col-6">
                             {isLogin
                                 ? <NavLink to={REGISTRATION_ROUTE}>Зарегестрироваться</NavLink>
                                 : <NavLink to={LOGIN_ROUTE}>Войти</NavLink>
                             }
                        </div>

                        {isLogin
                            ?
                                <Button
                                    className="col-6"
                                    variant={"outline-success"}
                                    onClick={click}
                                >
                                    Войти
                                </Button>
                            :
                                <Button
                                    className="col-6"
                                    variant={"outline-success"}
                                    onClick={click}
                                >
                                    Зарегестрироваться
                                </Button>
                        }
                    </Row>
                </Form>

            </Card>
        </Container>
    );
});

export default Auth;