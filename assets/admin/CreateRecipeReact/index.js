import React from 'react';
import ReactDOM from 'react-dom/client';
import App from "./App";

const reactForm = document.getElementById('reactForm')
const index = ReactDOM.createRoot(reactForm)

index.render (
    <>
        <App {...(reactForm.dataset)} />
    </>
)