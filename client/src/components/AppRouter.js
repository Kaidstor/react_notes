import React from 'react';
import {Route, Routes} from 'react-router-dom'
import {authRoutes, publicRoutes} from "../routes";
import TagsPage from "../page/TagsPage";
import Layout from "./Layout";
import {RequireAuth} from "../hoc/RequireAuth";
import Auth from "../page/Auth";


const AppRouter = () => {
    return (
        <Routes>
            <Route path="/" element={<Layout/>}>
                <Route index element={<RequireAuth><TagsPage/></RequireAuth>}/>
                {authRoutes.map(({path, Component}) =>
                    <Route key={path} path={path} element={<RequireAuth>{Component}</RequireAuth>}/>
                )}
                {publicRoutes.map(({path, Component}) =>
                    <Route key={path} path={path} element={Component}/>
                )}
                <Route path="*" element={<Auth/>}/>
            </Route>
        </Routes>
    );
};

export default AppRouter;