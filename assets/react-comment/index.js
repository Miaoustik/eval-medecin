import React from 'react';
import ReactDOM from 'react-dom/client';
import App from "./App";

const reactComments = document.getElementById('comments')
const index = ReactDOM.createRoot(reactComments)

index.render (
    <>
        <App {...(reactComments.dataset)} />
    </>
)