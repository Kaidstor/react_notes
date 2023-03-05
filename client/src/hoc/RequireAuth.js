import {Navigate, useLocation} from "react-router-dom";
import {useContext} from "react";
import {Context} from "../index";
import {observer} from "mobx-react-lite";

export const RequireAuth = observer(({children}) => {
    const {user} = useContext(Context)
    const location = useLocation()
    if (!user.isAuth)
        return <Navigate to='/login' state={{from: location.pathname}}/>

    return children
})