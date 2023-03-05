import React, {useContext} from 'react';
import {observer} from "mobx-react-lite";
import {Context} from "../index";
import {NavLink, Outlet, useNavigate} from "react-router-dom";
import {LOGIN_ROUTE, TAGS_ROUTE} from "../utils/consts";
import {Button, Container, Nav, Navbar} from "react-bootstrap";

const Layout = observer(() => {
    const {user} = useContext(Context)
    const navigate = useNavigate()
    const logOut = () => {
        user.setUser({})
        user.setIsAuth(false)
        localStorage.removeItem('token')
        navigate('/' + LOGIN_ROUTE)
    }

    if (user.isAuth)
        return (
            <>
                <Navbar bg="dark" expand="lg">
                    <Container>
                        <NavLink style={{color: 'white'}} to={'/' + TAGS_ROUTE}>HOME</NavLink>
                        <Nav
                            className="ml-auto my-2"
                            style={{maxHeight: '100px', color: 'white'}}
                            navbarScroll
                        >
                            <Button>Админ панель</Button>
                            <Button className="ms-3" onClick={logOut}>Выйти</Button>
                        </Nav>
                    </Container>
                </Navbar>
                <Outlet/>
            </>
        );
    return (<Outlet/>)
});

export default Layout;