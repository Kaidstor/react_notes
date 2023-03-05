import {ADMIN_ROUTE, LOGIN_ROUTE, NOTES_ROUTE, REGISTRATION_ROUTE, TAGS_ROUTE} from "./utils/consts";
import Admin from "./page/Admin";
import NotePage from "./page/NotePage";
import Auth from "./page/Auth";
import TagsPage from "./page/TagsPage";
import NotesPage from "./page/NotesPage";

export const authRoutes = [
    {
        path: ADMIN_ROUTE,
        Component: <Admin/>
    },
    {
        path: NOTES_ROUTE,
        Component: <NotesPage/>
    },
    {
        path: NOTES_ROUTE + '/:id',
        Component: <NotePage/>
    },
    {
        path: TAGS_ROUTE,
        Component: <TagsPage/>
    },
    {
        path: TAGS_ROUTE + '/:id',
        Component: <TagsPage/>
    }
]

export const publicRoutes = [
    {
        path: REGISTRATION_ROUTE,
        Component: <Auth/>
    },
    {
        path: LOGIN_ROUTE,
        Component: <Auth/>
    },
]