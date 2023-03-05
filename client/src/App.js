import AppRouter from "./components/AppRouter";
import React from "react";
import './App.css'
import FileUpload from "./components/FileUpload";

const App = () => {
    return (
        <FileUpload>
            <AppRouter/>
        </FileUpload>
    )
}

export default App;
